<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets\Query;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use InvalidArgumentException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;

/**
 * Database queries on the order items for the dashboard widgets.
 *
 * Fields can be given with or without the table name; only fields of the
 * order item table are accepted.
 */
class OrderItemQuery
{
    private const TABLE = 'tx_cart_domain_model_order_item';

    private const STATUS_TABLES = [
        'payment' => 'tx_cart_domain_model_order_payment',
        'shipping' => 'tx_cart_domain_model_order_shipping',
    ];

    public function __construct(
        private readonly QueryBuilder $queryBuilder,
    ) {
    }

    /**
     * Returns date and value of each order item with a date in the period.
     *
     * @param string $dateField field with the timestamp of the order item
     * @param null|string $valueField field with the value, null to count the order items (value 1)
     *
     * @return iterable<array{date: int, value: mixed}>
     */
    public function findValuesInPeriod(
        OrderItemCriteria $criteria,
        string $dateField,
        ?string $valueField,
        int $start,
        int $end,
    ): iterable {
        $queryBuilder = $this->createPeriodQueryBuilder($criteria, $dateField, $start, $end);
        $queryBuilder->addSelect(self::getQualifiedField($dateField) . ' AS date');
        if ($valueField === null) {
            $queryBuilder->addSelectLiteral('1 AS value');
        } else {
            $queryBuilder->addSelect(self::getQualifiedField($valueField) . ' AS value');
        }

        foreach ($queryBuilder->executeQuery()->iterateAssociative() as $row) {
            yield [
                'date' => is_numeric($row['date']) ? (int) $row['date'] : 0,
                'value' => $row['value'],
            ];
        }
    }

    /**
     * Returns the number of order items with a date in the period.
     *
     * @param string $dateField field with the timestamp of the order item
     */
    public function countInPeriod(
        OrderItemCriteria $criteria,
        string $dateField,
        int $start,
        int $end,
    ): int {
        $queryBuilder = $this->createPeriodQueryBuilder($criteria, $dateField, $start, $end);
        $queryBuilder->count(self::TABLE . '.uid');

        $count = $queryBuilder->executeQuery()->fetchOne();

        return is_numeric($count) ? (int) $count : 0;
    }

    /**
     * Returns the sum of a field of the order items with a date in the period.
     *
     * @param string $dateField field with the timestamp of the order item
     * @param string $valueField field with the value to sum up
     */
    public function sumInPeriod(
        OrderItemCriteria $criteria,
        string $dateField,
        string $valueField,
        int $start,
        int $end,
    ): float {
        $queryBuilder = $this->createPeriodQueryBuilder($criteria, $dateField, $start, $end);
        $queryBuilder->addSelectLiteral(
            'SUM(' . $queryBuilder->quoteIdentifier(self::getQualifiedField($valueField)) . ') AS value'
        );

        $sum = $queryBuilder->executeQuery()->fetchOne();

        return is_numeric($sum) ? (float) $sum : 0.0;
    }

    /**
     * Returns the first order items with name and status of payment and
     * shipping (payment_name, payment_status, shipping_name, shipping_status).
     *
     * @return list<array<string, mixed>>
     */
    public function findLatest(
        OrderItemCriteria $criteria,
        string $orderBy,
        string $direction,
        int $limit,
    ): array {
        $queryBuilder = $this->createQueryBuilder($criteria);
        $queryBuilder
            ->addSelect(
                self::TABLE . '.uid',
                self::TABLE . '.pid',
                self::TABLE . '.order_number',
                self::TABLE . '.order_date',
                self::TABLE . '.invoice_number',
                self::TABLE . '.invoice_date'
            )
            ->orderBy(
                self::getQualifiedField($orderBy),
                mb_strtolower($direction) === 'asc' ? 'ASC' : 'DESC'
            )
            ->setMaxResults(max(1, $limit))
        ;

        foreach (self::STATUS_TABLES as $type => $table) {
            $queryBuilder
                ->leftJoin(self::TABLE, $table, $type, self::getJoinCondition($queryBuilder, $type))
                ->addSelect($type . '.name AS ' . $type . '_name', $type . '.status AS ' . $type . '_status')
            ;
        }
        self::addStatusFilter($queryBuilder, $criteria, true);

        return $queryBuilder->executeQuery()->fetchAllAssociative();
    }

    /**
     * @return list<int> storage folders of all order items
     */
    public function findStoragePids(): array
    {
        $queryBuilder = clone $this->queryBuilder;

        $pids = $queryBuilder
            ->select(self::TABLE . '.pid')
            ->distinct()
            ->from(self::TABLE)
            ->executeQuery()
            ->fetchFirstColumn()
        ;

        $storagePids = [];
        foreach ($pids as $pid) {
            if (is_numeric($pid) && (int) $pid > 0) {
                $storagePids[] = (int) $pid;
            }
        }

        return $storagePids;
    }

    /**
     * Creates a query builder for the order items of the criteria with a
     * date in the period.
     */
    private function createPeriodQueryBuilder(OrderItemCriteria $criteria, string $dateField, int $start, int $end): QueryBuilder
    {
        $dateField = self::getQualifiedField($dateField);

        $queryBuilder = $this->createQueryBuilder($criteria);
        $queryBuilder->andWhere(
            $queryBuilder->expr()->gte($dateField, $queryBuilder->createNamedParameter($start, Connection::PARAM_INT)),
            $queryBuilder->expr()->lte($dateField, $queryBuilder->createNamedParameter($end, Connection::PARAM_INT))
        );
        self::addStatusFilter($queryBuilder, $criteria);

        return $queryBuilder;
    }

    private function createQueryBuilder(OrderItemCriteria $criteria): QueryBuilder
    {
        $queryBuilder = clone $this->queryBuilder;
        $queryBuilder->from(self::TABLE);

        if ($criteria->storagePids === null) {
            return $queryBuilder;
        }

        if ($criteria->storagePids === []) {
            $queryBuilder->andWhere('1 = 0');
            return $queryBuilder;
        }

        $queryBuilder->andWhere(
            $queryBuilder->expr()->in(
                self::TABLE . '.pid',
                $queryBuilder->createNamedParameter($criteria->storagePids, Connection::PARAM_INT_ARRAY)
            )
        );

        return $queryBuilder;
    }

    /**
     * Restricts the query to the status of payment and shipping of the
     * criteria. The payment and shipping tables are joined with the aliases
     * "payment" and "shipping", unless $isJoined is true.
     */
    private static function addStatusFilter(QueryBuilder $queryBuilder, OrderItemCriteria $criteria, bool $isJoined = false): void
    {
        foreach ($criteria->statusFilter as $type => $statusList) {
            if (!array_key_exists($type, self::STATUS_TABLES) || $statusList === []) {
                continue;
            }

            if (!$isJoined) {
                $queryBuilder->join(self::TABLE, self::STATUS_TABLES[$type], $type, self::getJoinCondition($queryBuilder, $type));
            }

            $queryBuilder->andWhere(
                $queryBuilder->expr()->in(
                    $type . '.status',
                    $queryBuilder->createNamedParameter($statusList, Connection::PARAM_STR_ARRAY)
                )
            );
        }
    }

    private static function getJoinCondition(QueryBuilder $queryBuilder, string $type): string
    {
        return $queryBuilder->expr()->eq(
            self::TABLE . '.uid',
            $queryBuilder->quoteIdentifier($type . '.item')
        );
    }

    /**
     * @throws InvalidArgumentException if the field is no field of the order item table
     */
    private static function getQualifiedField(string $field): string
    {
        $fieldName = str_starts_with($field, self::TABLE . '.') ? mb_substr($field, mb_strlen(self::TABLE) + 1) : $field;

        if (!in_array($fieldName, ['uid', 'pid'], true) && !in_array($fieldName, self::getColumnNames(), true)) {
            throw new InvalidArgumentException(
                sprintf('"%s" is no field of the table %s.', $field, self::TABLE),
                1794946139
            );
        }

        return self::TABLE . '.' . $fieldName;
    }

    /**
     * @return list<string>
     */
    private static function getColumnNames(): array
    {
        $tca = $GLOBALS['TCA'] ?? null;
        if (!is_array($tca) || !is_array($tca[self::TABLE] ?? null) || !is_array($tca[self::TABLE]['columns'] ?? null)) {
            return [];
        }

        return array_map(strval(...), array_keys($tca[self::TABLE]['columns']));
    }
}
