<?php

declare(strict_types=1);

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;
use Extcode\Cart\OrderHistory\OrderChangeRepository;
use Extcode\Cart\Utility\Typo3GlobalsUtility;
use RuntimeException;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final readonly class OrderItemCleanupService
{
    public function __construct(
        private ConnectionPool $connectionPool,
    ) {
    }

    public function run(DateTimeImmutable $cutOffDate): void
    {
        self::deleteRecordsFromTable(
            'tx_cart_domain_model_order_item',
            $this->getRecordUidsToDelete(
                'tx_cart_domain_model_order_item',
                $cutOffDate
            )
        );

        // the order history has no deleted field, the changes of orders deleted before (e.g. by hand) are removed as well
        $this->deleteOrderChanges(
            $this->getRecordUidsToDelete(
                'tx_cart_domain_model_order_item',
                $cutOffDate,
                includeDeletedRecords: true
            )
        );
    }

    private function getRecordUidsToDelete(string $tableName, DateTimeImmutable $cutOffDate, bool $includeDeletedRecords = false): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($tableName);
        $queryBuilder->getRestrictions()->removeAll();
        if (!$includeDeletedRecords) {
            $queryBuilder->getRestrictions()->add(GeneralUtility::makeInstance(DeletedRestriction::class));
        }

        return $queryBuilder
            ->select('uid')
            ->from($tableName)
            ->where(
                $queryBuilder->expr()->lt(
                    'crdate',
                    $queryBuilder->createNamedParameter($cutOffDate->getTimestamp(), Connection::PARAM_INT)
                ),
            )
            ->executeQuery()
            ->fetchFirstColumn()
        ;
    }

    /**
     * @param array<array-key, mixed> $orderItemUids
     */
    private function deleteOrderChanges(array $orderItemUids): void
    {
        $orderItemUids = array_map(intval(...), array_filter($orderItemUids, is_numeric(...)));
        $connection = $this->connectionPool->getConnectionForTable(OrderChangeRepository::TABLE_NAME);

        foreach (array_chunk($orderItemUids, 1000) as $chunk) {
            $queryBuilder = $connection->createQueryBuilder();
            $queryBuilder
                ->delete(OrderChangeRepository::TABLE_NAME)
                ->where(
                    $queryBuilder->expr()->in(
                        'item',
                        $queryBuilder->createNamedParameter($chunk, Connection::PARAM_INT_ARRAY)
                    )
                )
                ->executeStatement()
            ;
        }
    }

    private static function deleteRecordsFromTable(string $tableName, array $recordUids): void
    {
        $dataHandler = GeneralUtility::makeInstance(DataHandler::class);
        $dataHandler->start(
            [],
            [
                $tableName => array_fill_keys($recordUids, [
                    'delete' => 1,
                ]),
            ],
            Typo3GlobalsUtility::getTypo3BackendUser()
        );
        $dataHandler->process_cmdmap();

        if ($dataHandler->errorLog !== []) {
            throw new RuntimeException(
                'Could not properly delete records for table: ' . $tableName . ', got the following errors: ' . implode(', ', array_filter($dataHandler->errorLog, is_string(...))),
                1751526777
            );
        }
    }
}
