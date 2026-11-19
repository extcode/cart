<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Repository\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;
use Extcode\Cart\Domain\Model\Order\Change\OrderChange;
use Extcode\Cart\Domain\Model\Order\Change\OrderChangeSource;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * The changes of the orders in tx_cart_domain_model_order_change.
 */
final readonly class OrderChangeRepository
{
    public const TABLE_NAME = 'tx_cart_domain_model_order_change';

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {
    }

    /**
     * @return int the uid of the stored change
     */
    public function add(OrderChange $orderChange): int
    {
        $connection = $this->connectionPool->getConnectionForTable(self::TABLE_NAME);
        $connection->insert(
            self::TABLE_NAME,
            [
                'item' => $orderChange->orderItem,
                'type' => $orderChange->type,
                'subject' => $orderChange->subject,
                'old_value' => $orderChange->oldValue,
                'new_value' => $orderChange->newValue,
                'data' => $orderChange->data === [] ? '' : json_encode($orderChange->data, JSON_THROW_ON_ERROR),
                'crdate' => $orderChange->date->getTimestamp(),
                'be_user' => $orderChange->backendUser,
                'source' => $orderChange->source->value ?? '',
                'initiator' => $orderChange->initiator,
            ],
            [
                'item' => Connection::PARAM_INT,
                'crdate' => Connection::PARAM_INT,
                'be_user' => Connection::PARAM_INT,
            ]
        );

        return (int) $connection->lastInsertId();
    }

    /**
     * @return list<OrderChange> the changes of the order, the latest one first
     */
    public function findByOrderItem(int $orderItemUid): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable(self::TABLE_NAME);
        $rows = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'item',
                    $queryBuilder->createNamedParameter($orderItemUid, Connection::PARAM_INT)
                )
            )
            ->orderBy('crdate', 'DESC')
            ->addOrderBy('uid', 'DESC')
            ->executeQuery()
            ->fetchAllAssociative()
        ;

        return array_map(self::createOrderChange(...), $rows);
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function createOrderChange(array $row): OrderChange
    {
        $data = is_string($row['data'] ?? null) && $row['data'] !== '' ? json_decode($row['data'], true) : [];

        return new OrderChange(
            orderItem: self::toInt($row['item'] ?? 0),
            type: self::toString($row['type'] ?? ''),
            oldValue: self::toString($row['old_value'] ?? ''),
            newValue: self::toString($row['new_value'] ?? ''),
            date: new DateTimeImmutable('@' . self::toInt($row['crdate'] ?? 0)),
            source: OrderChangeSource::tryFrom(self::toString($row['source'] ?? '')),
            backendUser: self::toInt($row['be_user'] ?? 0),
            initiator: self::toString($row['initiator'] ?? ''),
            subject: self::toString($row['subject'] ?? ''),
            data: is_array($data) ? array_filter($data, is_string(...), ARRAY_FILTER_USE_KEY) : [],
            uid: self::toInt($row['uid'] ?? 0),
        );
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
