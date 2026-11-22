<?php

declare(strict_types=1);

namespace Extcode\Cart\OrderStatus;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;
use Extcode\Cart\Domain\Model\Order\AbstractService;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Domain\Model\Order\Shipping;
use Extcode\Cart\Event\Order\AfterStatusChangeEvent;
use Extcode\Cart\Event\Order\BeforeStatusChangeEvent;
use Extcode\Cart\OrderHistory\OrderChange;
use Extcode\Cart\OrderHistory\OrderChangeRepository;
use Extcode\Cart\OrderHistory\OrderChangeSource;
use Extcode\Cart\OrderHistory\OrderChangeType;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Changes the payment or the shipping status of an order and writes the
 * change to the order history (tx_cart_domain_model_order_change).
 *
 * The status is only changed if the order still has the expected status
 * ($fromStatus). The update is atomic, so of several requests with the same
 * expected status only one changes the status.
 *
 * Listeners of the BeforeStatusChangeEvent can veto a change, the
 * AfterStatusChangeEvent is dispatched after a change.
 */
final readonly class StatusChangeService
{
    public function __construct(
        private ConnectionPool $connectionPool,
        private OrderStatusRegistry $orderStatusRegistry,
        private OrderChangeRepository $orderChangeRepository,
        private Context $context,
        private EventDispatcherInterface $eventDispatcher,
    ) {
    }

    /**
     * On success the new status is set to the given service as well. It is
     * not persisted again by Extbase as long as the service is not passed to
     * a repository; the clean state still contains the old status.
     *
     * Without a given source it is determined from the request (backend,
     * frontend or CLI), by default the one in $GLOBALS['TYPO3_REQUEST']. A
     * server-to-server request, e.g. the notification of a payment provider,
     * passes OrderChangeSource::Api.
     *
     * @param string $initiator who or what triggered the change if not a backend user, e.g. "cart_paypal" or "erp_sync"
     */
    public function change(
        AbstractService $service,
        string $toStatus,
        string $fromStatus,
        string $initiator = '',
        ?ServerRequestInterface $request = null,
        ?OrderChangeSource $source = null
    ): StatusChangeResult {
        if ($source === null) {
            $request ??= $GLOBALS['TYPO3_REQUEST'] ?? null;
            $source = OrderChangeSource::fromRequest($request instanceof ServerRequestInterface ? $request : null);
        }

        [$serviceType, $changeType] = self::getServiceTypeAndChangeType($service);
        $serviceUid = $service->getUid();
        $orderItem = $service->getItem();
        $orderItemUid = $orderItem?->getUid();
        if ($serviceUid === null || $orderItem === null || $orderItemUid === null) {
            throw new InvalidArgumentException('The status can only be changed for persisted orders.', 1795089001);
        }

        if (!$this->orderStatusRegistry->hasStatus($serviceType, $toStatus)) {
            return new StatusChangeResult(StatusChangeOutcome::UnknownStatus);
        }
        if ($toStatus === $fromStatus) {
            return new StatusChangeResult(StatusChangeOutcome::Unchanged);
        }

        $backendUser = $this->getBackendUserUid();

        $beforeStatusChangeEvent = new BeforeStatusChangeEvent(
            $orderItem,
            $service,
            $serviceType,
            $fromStatus,
            $toStatus,
            $source,
            $initiator,
            $backendUser
        );
        $this->eventDispatcher->dispatch($beforeStatusChangeEvent);
        if ($beforeStatusChangeEvent->isVetoed()) {
            return new StatusChangeResult(StatusChangeOutcome::Vetoed, vetoReason: $beforeStatusChangeEvent->getVetoReason());
        }

        $table = 'tx_cart_domain_model_order_' . $serviceType->value;
        $connection = $this->connectionPool->getConnectionForTable($table);
        $orderChange = new OrderChange(
            orderItem: $orderItemUid,
            type: $changeType,
            oldValue: $fromStatus,
            newValue: $toStatus,
            date: $this->getNow(),
            source: $source,
            backendUser: $backendUser,
            initiator: $initiator,
        );

        $result = $connection->transactional(
            function (Connection $connection) use ($table, $serviceUid, $toStatus, $fromStatus, $orderChange): StatusChangeResult {
                $affectedRows = $connection->update(
                    $table,
                    [
                        'status' => $toStatus,
                        'tstamp' => $orderChange->date->getTimestamp(),
                    ],
                    [
                        'uid' => $serviceUid,
                        'status' => $fromStatus,
                    ],
                    [
                        'tstamp' => Connection::PARAM_INT,
                        'uid' => Connection::PARAM_INT,
                    ]
                );

                if ($affectedRows === 0) {
                    $storedStatus = $this->getStoredStatus($table, $serviceUid);

                    return $storedStatus === $toStatus
                        ? new StatusChangeResult(StatusChangeOutcome::Unchanged)
                        : new StatusChangeResult(StatusChangeOutcome::Stale, storedStatus: $storedStatus);
                }

                return new StatusChangeResult(
                    StatusChangeOutcome::Changed,
                    $orderChange->withUid($this->orderChangeRepository->add($orderChange))
                );
            }
        );

        if ($result->orderChange !== null) {
            $service->setStatus($toStatus);
            // after the transaction, listeners may take time, e.g. to send an email
            $afterStatusChangeEvent = new AfterStatusChangeEvent($orderItem, $service, $serviceType, $result->orderChange);
            $this->eventDispatcher->dispatch($afterStatusChangeEvent);
            if ($afterStatusChangeEvent->getErrors() !== []) {
                return new StatusChangeResult($result->outcome, $result->orderChange, errors: $afterStatusChangeEvent->getErrors());
            }
        }

        return $result;
    }

    /**
     * @return array{ServiceType, string}
     */
    private static function getServiceTypeAndChangeType(AbstractService $service): array
    {
        return match (true) {
            $service instanceof Payment => [ServiceType::Payment, OrderChangeType::PAYMENT_STATUS],
            $service instanceof Shipping => [ServiceType::Shipping, OrderChangeType::SHIPPING_STATUS],
            default => throw new InvalidArgumentException(
                'The status can only be changed for a payment or a shipping, ' . $service::class . ' given.',
                1795089002
            ),
        };
    }

    private function getStoredStatus(string $table, int $serviceUid): ?string
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($table);
        $queryBuilder->getRestrictions()->removeAll();
        $status = $queryBuilder
            ->select('status')
            ->from($table)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($serviceUid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchOne()
        ;

        return is_string($status) ? $status : null;
    }

    private function getNow(): DateTimeImmutable
    {
        $now = $this->context->getPropertyFromAspect('date', 'full');

        return $now instanceof DateTimeImmutable ? $now : new DateTimeImmutable();
    }

    private function getBackendUserUid(): int
    {
        $uid = $this->context->getPropertyFromAspect('backend.user', 'id', 0);

        return is_int($uid) ? $uid : 0;
    }
}
