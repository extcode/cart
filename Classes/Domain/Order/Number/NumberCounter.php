<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Number;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Doctrine\DBAL\Exception\UniqueConstraintViolationException;
use Doctrine\DBAL\Platforms\SQLitePlatform;
use RuntimeException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;

/**
 * Increments the counters of the order, invoice and delivery numbers.
 *
 * The counters are stored in the sys_registry with the namespace "tx_cart",
 * like the TYPO3 Registry does, so existing counters are continued. Unlike
 * the Registry, the counter is read and written in one transaction and the
 * row is locked (SELECT ... FOR UPDATE, on SQLite by a write first), so
 * concurrent requests never get the same number. The Registry is not used
 * because it caches the entries of a namespace for the whole request.
 */
final readonly class NumberCounter
{
    private const TABLE = 'sys_registry';

    private const NAMESPACE = 'tx_cart';

    private const MAX_ATTEMPTS = 3;

    public function __construct(
        private ConnectionPool $connectionPool,
    ) {
    }

    /**
     * @return positive-int the incremented counter, 1 for a new counter
     */
    public function increment(string $key): int
    {
        $connection = $this->connectionPool->getConnectionForTable(self::TABLE);

        for ($attempt = 1; ; $attempt++) {
            try {
                return $connection->transactional(
                    static fn (Connection $connection): int => self::incrementInTransaction($connection, $key)
                );
            } catch (UniqueConstraintViolationException $exception) {
                // a concurrent request created the counter, it is locked by SELECT ... FOR UPDATE now
                if ($attempt >= self::MAX_ATTEMPTS) {
                    throw new RuntimeException(
                        'The counter "' . $key . '" could not be created.',
                        1795506001,
                        $exception
                    );
                }
            }
        }
    }

    /**
     * @return positive-int
     */
    private static function incrementInTransaction(Connection $connection, string $key): int
    {
        $identifier = ['entry_namespace' => self::NAMESPACE, 'entry_key' => $key];
        $isSqlite = $connection->getDatabasePlatform() instanceof SQLitePlatform;
        if ($isSqlite) {
            // SQLite does not support SELECT ... FOR UPDATE, a write locks the database until the commit
            $connection->executeStatement(
                'UPDATE ' . $connection->quoteIdentifier(self::TABLE)
                . ' SET ' . $connection->quoteIdentifier('entry_value') . ' = ' . $connection->quoteIdentifier('entry_value')
                . ' WHERE ' . $connection->quoteIdentifier('entry_namespace') . ' = ?'
                . ' AND ' . $connection->quoteIdentifier('entry_key') . ' = ?',
                array_values($identifier)
            );
        }

        $queryBuilder = $connection->createQueryBuilder();
        $queryBuilder->getRestrictions()->removeAll();
        $queryBuilder
            ->select('entry_value')
            ->from(self::TABLE)
            ->where(
                $queryBuilder->expr()->eq('entry_namespace', $queryBuilder->createNamedParameter(self::NAMESPACE)),
                $queryBuilder->expr()->eq('entry_key', $queryBuilder->createNamedParameter($key))
            )
        ;
        if (!$isSqlite) {
            $queryBuilder->getConcreteQueryBuilder()->forUpdate();
        }

        $storedValue = $queryBuilder->executeQuery()->fetchOne();

        if ($storedValue === false) {
            $connection->insert(
                self::TABLE,
                [
                    'entry_namespace' => self::NAMESPACE,
                    'entry_key' => $key,
                    'entry_value' => serialize(1),
                ],
                ['entry_value' => Connection::PARAM_LOB]
            );

            return 1;
        }

        $number = max(0, self::unserializeCounter($storedValue)) + 1;

        $connection->update(
            self::TABLE,
            ['entry_value' => serialize($number)],
            $identifier,
            ['entry_value' => Connection::PARAM_LOB]
        );

        return $number;
    }

    private static function unserializeCounter(mixed $storedValue): int
    {
        if (is_resource($storedValue)) {
            $storedValue = stream_get_contents($storedValue);
        }
        if (!is_string($storedValue)) {
            return 0;
        }

        $value = unserialize($storedValue, ['allowed_classes' => false]);

        return is_numeric($value) ? (int) $value : 0;
    }
}
