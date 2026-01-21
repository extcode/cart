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
use Extcode\Cart\Utility\Typo3GlobalsUtility;
use RuntimeException;
use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\DataHandling\DataHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

final readonly class OrderItemCleanupService
{
    public function __construct(
        private ConnectionPool $connectionPool,
    )
    {
    }

    public function run(DateTimeImmutable $cutOffDate): void
    {
        $this->deleteRecordsFromTable(
            'tx_cart_domain_model_order_item',
            $this->getRecordUidsToDelete(
                'tx_cart_domain_model_order_item',
                $cutOffDate
            )
        );
    }

    private function getRecordUidsToDelete(string $tableName, DateTimeImmutable $cutOffDate): array
    {
        $queryBuilder = $this->connectionPool->getQueryBuilderForTable($tableName);
        $queryBuilder
            ->getRestrictions()
            ->removeAll()
            ->add(GeneralUtility::makeInstance(DeletedRestriction::class));

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
            ->fetchFirstColumn();
    }

    private function deleteRecordsFromTable(string $tableName, array $recordUids): void
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
                'Could not properly delete records for table: ' . $tableName . ', got the following errors: ' . implode(', ', $dataHandler->errorLog),
                1751526777
            );
        }
    }
}
