<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Doctrine\DBAL\Platforms\SQLitePlatform;
use InvalidArgumentException;
use LogicException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Concurrent requests check and write the quantities of an order one after
 * the other, so they can not ship more than ordered.
 *
 * @internal
 */
final readonly class OrderItemLock
{
    private const ORDER_ITEM_TABLE_NAME = 'tx_cart_domain_model_order_item';

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {
    }

    /**
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function lock(int $orderItemUid): void
    {
        $connection = $this->connectionPool->getConnectionForTable(self::ORDER_ITEM_TABLE_NAME);
        if (!$connection->isTransactionActive()) {
            throw new LogicException('The order can only be locked within a transaction.', 1795600001);
        }

        if ($connection->getDatabasePlatform() instanceof SQLitePlatform) {
            // SQLite does not support SELECT ... FOR UPDATE, a write locks the database until the commit
            $affectedRows = $connection->executeStatement(
                'UPDATE ' . $connection->quoteIdentifier(self::ORDER_ITEM_TABLE_NAME)
                . ' SET ' . $connection->quoteIdentifier('uid') . ' = ' . $connection->quoteIdentifier('uid')
                . ' WHERE ' . $connection->quoteIdentifier('uid') . ' = ?',
                [$orderItemUid],
                [Connection::PARAM_INT]
            );
            if ($affectedRows === 0) {
                throw self::createOrderNotFoundException($orderItemUid);
            }

            return;
        }

        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('uid')
            ->from(self::ORDER_ITEM_TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('uid', $queryBuilder->createNamedParameter($orderItemUid, Connection::PARAM_INT))
            )
        ;
        $queryBuilder->getConcreteQueryBuilder()->forUpdate();

        if ($queryBuilder->executeQuery()->fetchOne() === false) {
            throw self::createOrderNotFoundException($orderItemUid);
        }
    }

    private static function createOrderNotFoundException(int $orderItemUid): InvalidArgumentException
    {
        return new InvalidArgumentException('The order item ' . $orderItemUid . ' does not exist.', 1795600002);
    }
}
