<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;
use Extcode\Cart\Domain\Order\History\OrderChange;
use Extcode\Cart\Domain\Order\History\OrderChangeSource;
use Extcode\Cart\Domain\Order\History\OrderChangeType;
use Extcode\Cart\Domain\Order\Status\OrderStatusRegistry;
use Extcode\Cart\Domain\Order\Status\ServiceType;
use Extcode\Cart\Domain\Order\Status\StatusChangeOutcome;
use Extcode\Cart\Domain\Order\Status\StatusChangeResult;
use Extcode\Cart\Domain\Order\Status\StatusTransition;
use Extcode\Cart\Event\Order\AfterShipmentStatusChangeEvent;
use Extcode\Cart\Event\Order\BeforeShipmentStatusChangeEvent;
use InvalidArgumentException;
use Psr\EventDispatcher\EventDispatcherInterface;
use Psr\Http\Message\ServerRequestInterface;

final readonly class ShipmentStatusChangeService
{
    private const NOT_CANCELABLE_STATUSES = [
        ShipmentStatus::SHIPPED,
        ShipmentStatus::DELIVERED,
    ];

    public function __construct(
        private ShipmentRepository $shipmentRepository,
        private OrderStatusRegistry $orderStatusRegistry,
        private StatusTransition $statusTransition,
        private EventDispatcherInterface $eventDispatcher,
        private OrderShippingStatusUpdater $orderShippingStatusUpdater,
    ) {
    }

    public function change(
        Shipment $shipment,
        string $toStatus,
        string $fromStatus,
        string $initiator = '',
        ?ServerRequestInterface $request = null,
        ?OrderChangeSource $source = null
    ): ShipmentStatusChangeResult {
        $source = $this->statusTransition->resolveSource($request, $source);

        if ($shipment->uid === 0) {
            throw new InvalidArgumentException('The status can only be changed for stored shipments.', 1795600020);
        }

        if (!$this->orderStatusRegistry->hasStatus(ServiceType::Shipment, $toStatus)) {
            return new ShipmentStatusChangeResult(new StatusChangeResult(StatusChangeOutcome::UnknownStatus));
        }

        if ($toStatus === $fromStatus) {
            return new ShipmentStatusChangeResult(new StatusChangeResult(StatusChangeOutcome::Unchanged));
        }

        if (!self::isTransitionAllowed($fromStatus, $toStatus)) {
            return new ShipmentStatusChangeResult(new StatusChangeResult(StatusChangeOutcome::NotAllowed));
        }

        $backendUserUid = $this->statusTransition->getBackendUserUid();
        $beforeShipmentStatusChangeEvent = new BeforeShipmentStatusChangeEvent(
            $shipment,
            $fromStatus,
            $toStatus,
            $source,
            $initiator,
            $backendUserUid
        );
        $this->eventDispatcher->dispatch($beforeShipmentStatusChangeEvent);
        if ($beforeShipmentStatusChangeEvent->isVetoed()) {
            return new ShipmentStatusChangeResult(
                new StatusChangeResult(StatusChangeOutcome::Vetoed, vetoReason: $beforeShipmentStatusChangeEvent->getVetoReason())
            );
        }

        $result = $this->statusTransition->apply(
            new OrderChange(
                orderItem: $shipment->orderItemUid,
                type: OrderChangeType::SHIPMENT_STATUS,
                oldValue: $fromStatus,
                newValue: $toStatus,
                date: $this->statusTransition->getNow(),
                source: $source,
                backendUser: $backendUserUid,
                initiator: $initiator,
                subject: $shipment->getOrderChangeSubject(),
            ),
            fn (string $fromStatus, string $toStatus, DateTimeImmutable $date): bool => $this->shipmentRepository->changeStatusIfExpected($shipment->uid, $fromStatus, $toStatus, $date),
            fn (): ?string => $this->shipmentRepository->findStatusByShipmentUid($shipment->uid),
        );

        if ($result->orderChange === null) {
            return new ShipmentStatusChangeResult($result);
        }

        // after the transaction, listeners may take time, e.g. to send an email
        $afterShipmentStatusChangeEvent = new AfterShipmentStatusChangeEvent($shipment->withStatus($toStatus), $result->orderChange);
        $this->eventDispatcher->dispatch($afterShipmentStatusChangeEvent);
        if ($afterShipmentStatusChangeEvent->getErrors() !== []) {
            $result = new StatusChangeResult($result->outcome, $result->orderChange, errors: $afterShipmentStatusChangeEvent->getErrors());
        }

        return new ShipmentStatusChangeResult(
            $result,
            $this->orderShippingStatusUpdater->changeToDerivedStatus($shipment->orderItemUid, $initiator, $request, $source)
        );
    }

    private static function isTransitionAllowed(string $fromStatus, string $toStatus): bool
    {
        if ($fromStatus === ShipmentStatus::CANCELED) {
            return false;
        }

        return $toStatus !== ShipmentStatus::CANCELED || !in_array($fromStatus, self::NOT_CANCELABLE_STATUSES, true);
    }
}
