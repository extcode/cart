<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Log\Repository;

use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final readonly class LogRepository
{
    public const TABLE_NAME = 'tx_cart_domain_model_order_log';

    private QueryBuilder $queryBuilder;

    public function __construct(
    ) {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $this->queryBuilder = $connectionPool
            ->getQueryBuilderForTable(self::TABLE_NAME)
        ;
    }

    public function insert(
        array $fieldValues,
    ): void {
        // for cleanup of table
        $fieldValues['crdate'] = time();

        $queryBuilder = clone $this->queryBuilder;
        $queryBuilder
            ->insert(self::TABLE_NAME)
            ->values($fieldValues)
            ->executeStatement()
        ;
    }

    public function findAllByIdentifier(
        string $identifier,
    ): array {
        $queryBuilder = clone $this->queryBuilder;
        $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq(
                    'identifier',
                    $queryBuilder->createNamedParameter($identifier)
                )
            )
        ;

        return $queryBuilder
            ->executeQuery()
            ->fetchAllAssociative()
        ;
    }
}
