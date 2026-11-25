<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

final readonly class OrderProductQuantityCalculator
{
    private const ORDER_PRODUCT_TABLE_NAME = 'tx_cart_domain_model_order_product';

    private const SHIPPED_STATUSES = [
        ShipmentStatus::SHIPPED,
        ShipmentStatus::DELIVERED,
    ];

    public function __construct(
        private ConnectionPool $connectionPool,
        private ShipmentRepository $shipmentRepository,
    ) {
    }

    public function calculateForOrderItem(int $orderItemUid): OrderProductQuantities
    {
        $countInUncanceledShipmentsByOrderProductUid = [];
        $shippedCountByOrderProductUid = [];
        foreach ($this->shipmentRepository->findByOrderItem($orderItemUid) as $shipment) {
            if ($shipment->status === ShipmentStatus::CANCELED) {
                continue;
            }
            $isShipped = in_array($shipment->status, self::SHIPPED_STATUSES, true);
            foreach ($shipment->products as $shipmentProduct) {
                $orderProductUid = $shipmentProduct->orderProductUid;
                $countInUncanceledShipmentsByOrderProductUid[$orderProductUid]
                    = ($countInUncanceledShipmentsByOrderProductUid[$orderProductUid] ?? 0) + $shipmentProduct->count;
                if ($isShipped) {
                    $shippedCountByOrderProductUid[$orderProductUid]
                        = ($shippedCountByOrderProductUid[$orderProductUid] ?? 0) + $shipmentProduct->count;
                }
            }
        }

        return new OrderProductQuantities(...array_map(
            static function (array $orderProductRow) use ($countInUncanceledShipmentsByOrderProductUid, $shippedCountByOrderProductUid): OrderProductQuantity {
                $orderProductUid = self::toInt($orderProductRow['uid'] ?? 0);

                return new OrderProductQuantity(
                    orderProductUid: $orderProductUid,
                    isShippable: (bool) self::toInt($orderProductRow['shippable'] ?? 0),
                    orderedCount: self::toInt($orderProductRow['count'] ?? 0),
                    countInUncanceledShipments: $countInUncanceledShipmentsByOrderProductUid[$orderProductUid] ?? 0,
                    shippedCount: $shippedCountByOrderProductUid[$orderProductUid] ?? 0,
                );
            },
            $this->findOrderProductRows($orderItemUid)
        ));
    }

    /**
     * @return list<array<string, mixed>>
     */
    private function findOrderProductRows(int $orderItemUid): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::ORDER_PRODUCT_TABLE_NAME);

        return $queryBuilder
            ->select('uid', 'count', 'shippable')
            ->from(self::ORDER_PRODUCT_TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('item', $queryBuilder->createNamedParameter($orderItemUid, Connection::PARAM_INT))
            )
            ->orderBy('uid', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative()
        ;
    }

    private static function toInt(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }
}
