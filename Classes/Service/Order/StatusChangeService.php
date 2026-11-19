<?php

declare(strict_types=1);

namespace Extcode\Cart\Service\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;
use Extcode\Cart\Configuration\OrderStatus\OrderStatusRegistry;
use Extcode\Cart\Domain\Model\Order\AbstractService;
use Extcode\Cart\Domain\Model\Order\Change\OrderChange;
use Extcode\Cart\Domain\Model\Order\Change\OrderChangeSource;
use Extcode\Cart\Domain\Model\Order\Change\OrderChangeType;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Domain\Model\Order\Shipping;
use Extcode\Cart\Domain\Model\Order\Status\ServiceType;
use Extcode\Cart\Domain\Repository\Order\OrderChangeRepository;
use InvalidArgumentException;
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
 */
final readonly class StatusChangeService
{
    public function __construct(
        private ConnectionPool $connectionPool,
        private OrderStatusRegistry $orderStatusRegistry,
        private OrderChangeRepository $orderChangeRepository,
        private Context $context,
    ) {
    }

    /**
     * On success the new status is set to the given service as well. It is
     * not persisted again by Extbase as long as the service is not passed to
     * a repository; the clean state still contains the old status.
     *
     * The source (backend, frontend or CLI) is determined from the request,
     * by default the one in $GLOBALS['TYPO3_REQUEST'].
     *
     * @param string $initiator who or what triggered the change if not a backend user, e.g. "cart_paypal" or "erp_sync"
     */
    public function change(
        AbstractService $service,
        string $toStatus,
        string $fromStatus,
        string $initiator = '',
        ?ServerRequestInterface $request = null
    ): StatusChangeResult {
        $request ??= $GLOBALS['TYPO3_REQUEST'] ?? null;
        $source = OrderChangeSource::fromRequest($request instanceof ServerRequestInterface ? $request : null);

        [$serviceType, $changeType] = self::getServiceTypeAndChangeType($service);
        $serviceUid = $service->getUid();
        $orderItemUid = $service->getItem()?->getUid();
        if ($serviceUid === null || $orderItemUid === null) {
            throw new InvalidArgumentException('The status can only be changed for persisted orders.', 1795089001);
        }

        if (!$this->orderStatusRegistry->hasStatus($serviceType, $toStatus)) {
            return StatusChangeResult::UnknownStatus;
        }
        if ($toStatus === $fromStatus) {
            return StatusChangeResult::Unchanged;
        }

        $table = 'tx_cart_domain_model_order_' . $serviceType->value;
        $connection = $this->connectionPool->getConnectionForTable($table);
        $now = $this->getNow();

        $result = $connection->transactional(
            function (Connection $connection) use ($table, $serviceUid, $orderItemUid, $toStatus, $fromStatus, $changeType, $source, $initiator, $now): StatusChangeResult {
                $affectedRows = $connection->update(
                    $table,
                    [
                        'status' => $toStatus,
                        'tstamp' => $now->getTimestamp(),
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
                    return $this->getStoredStatus($table, $serviceUid) === $toStatus
                        ? StatusChangeResult::Unchanged
                        : StatusChangeResult::Stale;
                }

                $this->orderChangeRepository->add(
                    new OrderChange(
                        orderItem: $orderItemUid,
                        type: $changeType,
                        oldValue: $fromStatus,
                        newValue: $toStatus,
                        date: $now,
                        source: $source,
                        backendUser: $this->getBackendUserUid(),
                        initiator: $initiator,
                    )
                );

                return StatusChangeResult::Changed;
            }
        );

        if ($result === StatusChangeResult::Changed) {
            $service->setStatus($toStatus);
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
