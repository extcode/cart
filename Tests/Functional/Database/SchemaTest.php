<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Database;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Database\Schema\SchemaMigrator;
use TYPO3\CMS\Core\Database\Schema\SqlReader;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversNothing]
final class SchemaTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'extcode/cart',
    ];

    #[Test]
    public function databaseCompareSuggestsNoChangesForTheTablesOfCart(): void
    {
        $sqlReader = $this->get(SqlReader::class);
        $statements = $sqlReader->getCreateTableStatementArray($sqlReader->getTablesDefinitionString());

        $suggestions = $this->get(SchemaMigrator::class)->getUpdateSuggestions($statements);

        $changes = [];
        foreach ($suggestions as $suggestionsOfConnection) {
            foreach ($suggestionsOfConnection as $type => $statementsOfType) {
                foreach ($statementsOfType as $statement) {
                    if (str_contains($statement, 'tx_cart_')) {
                        $changes[] = $type . ': ' . $statement;
                    }
                }
            }
        }

        self::assertSame([], $changes);
    }
}
