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
use InvalidArgumentException;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

final readonly class ShipmentRepository
{
    private const ORDER_SHIPMENT_TABLE_NAME = 'tx_cart_domain_model_order_shipment';

    private const ORDER_SHIPMENT_PRODUCT_TABLE_NAME = 'tx_cart_domain_model_order_shipment_product';

    private const ORDER_ITEM_TABLE_NAME = 'tx_cart_domain_model_order_item';

    public function __construct(
        private ConnectionPool $connectionPool,
        private Context $context,
    ) {
    }

    /**
     * @return int the uid of the stored shipment
     */
    public function add(Shipment $shipment): int
    {
        $orderItemPid = $this->findPidOfOrderItem($shipment->orderItemUid);
        $timestamp = $this->getCurrentTimestamp();

        return $this->connectionPool->getConnectionForTable(self::ORDER_SHIPMENT_TABLE_NAME)->transactional(
            static function (Connection $connection) use ($shipment, $orderItemPid, $timestamp): int {
                $connection->insert(
                    self::ORDER_SHIPMENT_TABLE_NAME,
                    [
                        'pid' => $orderItemPid,
                        'tstamp' => $timestamp,
                        'crdate' => $timestamp,
                        'item' => $shipment->orderItemUid,
                        'status' => $shipment->status,
                        'carrier' => $shipment->carrier,
                        'tracking_number' => $shipment->trackingNumber,
                        'tracking_url' => $shipment->trackingUrl,
                        'shipped_date' => $shipment->shippedDate?->getTimestamp() ?? 0,
                        'note' => $shipment->note,
                        'replacement' => (int) $shipment->isReplacement,
                        'products' => count($shipment->products),
                    ],
                    [
                        'pid' => Connection::PARAM_INT,
                        'tstamp' => Connection::PARAM_INT,
                        'crdate' => Connection::PARAM_INT,
                        'item' => Connection::PARAM_INT,
                        'shipped_date' => Connection::PARAM_INT,
                        'replacement' => Connection::PARAM_INT,
                        'products' => Connection::PARAM_INT,
                    ]
                );
                $shipmentUid = (int) $connection->lastInsertId();

                foreach ($shipment->products as $shipmentProduct) {
                    $connection->insert(
                        self::ORDER_SHIPMENT_PRODUCT_TABLE_NAME,
                        [
                            'pid' => $orderItemPid,
                            'tstamp' => $timestamp,
                            'crdate' => $timestamp,
                            'shipment' => $shipmentUid,
                            'order_product' => $shipmentProduct->orderProductUid,
                            'count' => $shipmentProduct->count,
                        ],
                        [
                            'pid' => Connection::PARAM_INT,
                            'tstamp' => Connection::PARAM_INT,
                            'crdate' => Connection::PARAM_INT,
                            'shipment' => Connection::PARAM_INT,
                            'order_product' => Connection::PARAM_INT,
                            'count' => Connection::PARAM_INT,
                        ]
                    );
                }

                $queryBuilder = $connection->createQueryBuilder();
                $queryBuilder
                    ->update(self::ORDER_ITEM_TABLE_NAME)
                    ->set('shipments', $queryBuilder->quoteIdentifier('shipments') . ' + 1', false)
                    ->where(
                        $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($shipment->orderItemUid, Connection::PARAM_INT))
                    )
                    ->executeStatement()
                ;

                return $shipmentUid;
            }
        );
    }

    public function changeStatusIfExpected(int $shipmentUid, string $fromStatus, string $toStatus, DateTimeImmutable $date): bool
    {
        $affectedRows = $this->connectionPool->getConnectionForTable(self::ORDER_SHIPMENT_TABLE_NAME)->update(
            self::ORDER_SHIPMENT_TABLE_NAME,
            [
                'status' => $toStatus,
                'tstamp' => $date->getTimestamp(),
            ],
            [
                'uid' => $shipmentUid,
                'status' => $fromStatus,
                'deleted' => 0,
            ],
            [
                'tstamp' => Connection::PARAM_INT,
                'uid' => Connection::PARAM_INT,
                'deleted' => Connection::PARAM_INT,
            ]
        );

        return $affectedRows > 0;
    }

    public function findStatusByShipmentUid(int $shipmentUid): ?string
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::ORDER_SHIPMENT_TABLE_NAME);
        $status = $queryBuilder
            ->select('status')
            ->from(self::ORDER_SHIPMENT_TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($shipmentUid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchOne()
        ;

        return is_string($status) ? $status : null;
    }

    public function findByUid(int $shipmentUid): ?Shipment
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::ORDER_SHIPMENT_TABLE_NAME);
        $shipmentRow = $queryBuilder
            ->select('*')
            ->from(self::ORDER_SHIPMENT_TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($shipmentUid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchAssociative()
        ;

        if ($shipmentRow === false) {
            return null;
        }

        $shipmentProducts = $this->findShipmentProductsByShipmentUids([$shipmentUid]);

        return self::mapRowToShipment($shipmentRow, $shipmentProducts[$shipmentUid] ?? []);
    }

    /**
     * @return list<Shipment> oldest first
     */
    public function findByOrderItem(int $orderItemUid): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::ORDER_SHIPMENT_TABLE_NAME);
        $shipmentRows = $queryBuilder
            ->select('*')
            ->from(self::ORDER_SHIPMENT_TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('item', $queryBuilder->createNamedParameter($orderItemUid, Connection::PARAM_INT))
            )
            ->orderBy('uid', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        $shipmentUids = array_map(static fn (array $shipmentRow): int => self::toInt($shipmentRow['uid'] ?? 0), $shipmentRows);
        $shipmentProducts = $this->findShipmentProductsByShipmentUids($shipmentUids);

        return array_map(
            static fn (array $shipmentRow): Shipment => self::mapRowToShipment(
                $shipmentRow,
                $shipmentProducts[self::toInt($shipmentRow['uid'] ?? 0)] ?? []
            ),
            $shipmentRows
        );
    }

    /**
     * @param list<int> $shipmentUids
     *
     * @return array<int, list<ShipmentProduct>> by shipment uid
     */
    private function findShipmentProductsByShipmentUids(array $shipmentUids): array
    {
        if ($shipmentUids === []) {
            return [];
        }

        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::ORDER_SHIPMENT_PRODUCT_TABLE_NAME);
        $shipmentProductRows = $queryBuilder
            ->select('*')
            ->from(self::ORDER_SHIPMENT_PRODUCT_TABLE_NAME)
            ->where(
                $queryBuilder->expr()->in(
                    'shipment',
                    $queryBuilder->createNamedParameter($shipmentUids, Connection::PARAM_INT_ARRAY)
                )
            )
            ->orderBy('uid', 'ASC')
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        $shipmentProductsByShipmentUid = [];
        foreach ($shipmentProductRows as $shipmentProductRow) {
            $shipmentUid = self::toInt($shipmentProductRow['shipment'] ?? 0);
            $shipmentProductsByShipmentUid[$shipmentUid][] = new ShipmentProduct(
                orderProductUid: self::toInt($shipmentProductRow['order_product'] ?? 0),
                count: self::toInt($shipmentProductRow['count'] ?? 0),
                uid: self::toInt($shipmentProductRow['uid'] ?? 0),
            );
        }

        return $shipmentProductsByShipmentUid;
    }

    private function findPidOfOrderItem(int $orderItemUid): int
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::ORDER_ITEM_TABLE_NAME);
        $orderItemPid = $queryBuilder
            ->select('pid')
            ->from(self::ORDER_ITEM_TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($orderItemUid, Connection::PARAM_INT))
            )
            ->executeQuery()
            ->fetchOne()
        ;

        if ($orderItemPid === false) {
            throw new InvalidArgumentException('The order item ' . $orderItemUid . ' does not exist.', 1795347001);
        }

        return self::toInt($orderItemPid);
    }

    /**
     * @param array<string, mixed> $shipmentRow
     * @param list<ShipmentProduct> $shipmentProducts
     */
    private static function mapRowToShipment(array $shipmentRow, array $shipmentProducts): Shipment
    {
        $shippedTimestamp = self::toInt($shipmentRow['shipped_date'] ?? 0);

        return new Shipment(
            orderItemUid: self::toInt($shipmentRow['item'] ?? 0),
            status: self::toString($shipmentRow['status'] ?? ''),
            products: $shipmentProducts,
            carrier: self::toString($shipmentRow['carrier'] ?? ''),
            trackingNumber: self::toString($shipmentRow['tracking_number'] ?? ''),
            trackingUrl: self::toString($shipmentRow['tracking_url'] ?? ''),
            shippedDate: $shippedTimestamp > 0 ? new DateTimeImmutable('@' . $shippedTimestamp) : null,
            note: self::toString($shipmentRow['note'] ?? ''),
            isReplacement: (bool) self::toInt($shipmentRow['replacement'] ?? 0),
            uid: self::toInt($shipmentRow['uid'] ?? 0),
        );
    }

    private function getCurrentTimestamp(): int
    {
        $now = $this->context->getPropertyFromAspect('date', 'full');

        return ($now instanceof DateTimeImmutable ? $now : new DateTimeImmutable())->getTimestamp();
    }

    private static function toInt(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }

    private static function toString(mixed $value): string
    {
        return is_scalar($value) ? (string) $value : '';
    }
}
