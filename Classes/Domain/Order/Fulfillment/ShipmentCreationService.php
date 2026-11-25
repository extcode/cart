<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\History\OrderChange;
use Extcode\Cart\Domain\Order\History\OrderChangeRepository;
use Extcode\Cart\Domain\Order\History\OrderChangeSource;
use Extcode\Cart\Domain\Order\History\OrderChangeType;
use Extcode\Cart\Domain\Order\Status\OrderStatusRegistry;
use Extcode\Cart\Domain\Order\Status\ServiceType;
use Extcode\Cart\Domain\Order\Status\StatusTransition;
use InvalidArgumentException;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use TYPO3\CMS\Core\Database\ConnectionPool;

final readonly class ShipmentCreationService
{
    private const ORDER_ITEM_TABLE_NAME = 'tx_cart_domain_model_order_item';

    public function __construct(
        private ConnectionPool $connectionPool,
        private OrderItemLock $orderItemLock,
        private OrderProductQuantityCalculator $orderProductQuantityCalculator,
        private ShipmentRepository $shipmentRepository,
        private OrderChangeRepository $orderChangeRepository,
        private OrderStatusRegistry $orderStatusRegistry,
        private StatusTransition $statusTransition,
        private OrderShippingStatusUpdater $orderShippingStatusUpdater,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     */
    public function create(
        Shipment $shipment,
        string $initiator = '',
        ?ServerRequestInterface $request = null,
        ?OrderChangeSource $source = null
    ): ShipmentCreationResult {
        $this->assertCanBeCreated($shipment);
        $source = $this->statusTransition->resolveSource($request, $source);

        $result = $this->connectionPool->getConnectionForTable(self::ORDER_ITEM_TABLE_NAME)->transactional(
            function () use ($shipment, $source, $initiator): ShipmentCreationResult {
                $this->orderItemLock->lock($shipment->orderItemUid);

                $exceededOrderProductUids = $this->findExceededOrderProductUids($shipment);
                if ($exceededOrderProductUids !== []) {
                    return new ShipmentCreationResult(
                        ShipmentCreationOutcome::QuantityExceeded,
                        exceededOrderProductUids: $exceededOrderProductUids
                    );
                }

                $storedShipment = $this->shipmentRepository->findByUid($this->shipmentRepository->add($shipment))
                    ?? throw new RuntimeException('The stored shipment could not be found.', 1795600013);
                $orderChange = new OrderChange(
                    orderItem: $shipment->orderItemUid,
                    type: OrderChangeType::SHIPMENT_STATUS,
                    oldValue: '',
                    newValue: $shipment->status,
                    date: $this->statusTransition->getNow(),
                    source: $source,
                    backendUser: $this->statusTransition->getBackendUserUid(),
                    initiator: $initiator,
                    subject: $storedShipment->getOrderChangeSubject(),
                    data: [
                        'products' => array_map(
                            static fn (ShipmentProduct $shipmentProduct): array => [
                                'orderProduct' => $shipmentProduct->orderProductUid,
                                'count' => $shipmentProduct->count,
                            ],
                            $storedShipment->products
                        ),
                    ],
                );

                return new ShipmentCreationResult(
                    ShipmentCreationOutcome::Created,
                    $storedShipment,
                    $orderChange->withUid($this->orderChangeRepository->add($orderChange))
                );
            }
        );

        if (!$result->isCreated()) {
            return $result;
        }

        return new ShipmentCreationResult(
            $result->outcome,
            $result->shipment,
            $result->orderChange,
            shippingStatusChange: $this->orderShippingStatusUpdater->changeToDerivedStatus($shipment->orderItemUid, $initiator, $request, $source),
        );
    }

    private function assertCanBeCreated(Shipment $shipment): void
    {
        if ($shipment->uid !== 0) {
            throw new InvalidArgumentException('The shipment ' . $shipment->uid . ' is stored already.', 1795600010);
        }
        if (
            $shipment->status === ShipmentStatus::CANCELED
            || !$this->orderStatusRegistry->hasStatus(ServiceType::Shipment, $shipment->status)
        ) {
            throw new InvalidArgumentException('A shipment can not be created with the status "' . $shipment->status . '".', 1795600011);
        }
        if ($shipment->products === []) {
            throw new InvalidArgumentException('A shipment needs at least one product.', 1795600012);
        }
        foreach ($shipment->products as $shipmentProduct) {
            if ($shipmentProduct->count <= 0) {
                throw new InvalidArgumentException(
                    'The count of the ordered product ' . $shipmentProduct->orderProductUid . ' must be greater than 0.',
                    1795600014
                );
            }
        }
    }

    /**
     * @return list<int>
     */
    private function findExceededOrderProductUids(Shipment $shipment): array
    {
        $orderProductQuantities = $this->orderProductQuantityCalculator->calculateForOrderItem($shipment->orderItemUid);

        $exceededOrderProductUids = [];
        foreach (self::sumCountsByOrderProductUid($shipment) as $orderProductUid => $count) {
            $orderProductQuantity = $orderProductQuantities->findByOrderProductUid($orderProductUid) ?? throw new InvalidArgumentException(
                'The ordered product ' . $orderProductUid . ' does not belong to the order item ' . $shipment->orderItemUid . '.',
                1795600015
            );
            if (!$orderProductQuantity->isShippable) {
                throw new InvalidArgumentException('The ordered product ' . $orderProductUid . ' is not shippable.', 1795600016);
            }
            if ($count > $orderProductQuantity->getCountAvailableForShipment()) {
                $exceededOrderProductUids[] = $orderProductUid;
            }
        }

        return $exceededOrderProductUids;
    }

    /**
     * @return array<int, int>
     */
    private static function sumCountsByOrderProductUid(Shipment $shipment): array
    {
        $countsByOrderProductUid = [];
        foreach ($shipment->products as $shipmentProduct) {
            $countsByOrderProductUid[$shipmentProduct->orderProductUid]
                = ($countsByOrderProductUid[$shipmentProduct->orderProductUid] ?? 0) + $shipmentProduct->count;
        }

        return $countsByOrderProductUid;
    }
}
