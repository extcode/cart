<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Acceptance\Support;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\PhpDataSet;
use Codeception\Event\SuiteEvent;
use DateTimeInterface;
use Extcode\TestingFramework\Core\Acceptance\Extension\BackendEnvironment;
use UnexpectedValueException;

final class Environment extends BackendEnvironment
{
    public function _initialize(): void
    {
        $this->localConfig = [
            'coreExtensionsToLoad' => [
                'typo3/cms-core',
                'typo3/cms-backend',
                'typo3/cms-extbase',
                'typo3/cms-frontend',
                'typo3/cms-fluid',
            ],
            'testExtensionsToLoad' => [
                'extcode/cart',
            ],
            'phpDatabaseFixtures' => [
                __DIR__ . '/../Fixtures/BackendUserDatabase.php',
                __DIR__ . '/../../Fixtures/BaseDatabase.php',
            ],
            'configurationToUseInTestInstance' => [
                'SYS' => [
                    'trustedHostsPattern' => '.*',
                ],
            ],
        ];

        parent::_initialize();
    }

    public function bootstrapTypo3Environment(SuiteEvent $suiteEvent): void
    {
        parent::bootstrapTypo3Environment($suiteEvent);

        assert(is_array($this->config['phpDatabaseFixtures']));
        foreach ($this->config['phpDatabaseFixtures'] as $dataSetFile) {
            assert(is_string($dataSetFile));
            (new PhpDataSet())->import(self::loadDataSet($dataSetFile));
        }
    }

    /**
     * @return array<string, array<int|string, array<int|string, null|DateTimeInterface|int|string>>>
     */
    private static function loadDataSet(string $dataSetFile): array
    {
        $dataSet = include $dataSetFile;
        if (!is_array($dataSet)) {
            throw new UnexpectedValueException('The data set ' . $dataSetFile . ' does not return an array.', 1794931200);
        }

        $validDataSet = [];
        foreach ($dataSet as $table => $records) {
            if (!is_string($table) || !is_array($records)) {
                throw new UnexpectedValueException('The data set ' . $dataSetFile . ' contains an invalid table.', 1794931201);
            }
            foreach ($records as $recordKey => $record) {
                if (!is_array($record)) {
                    throw new UnexpectedValueException('The data set ' . $dataSetFile . ' contains an invalid record in ' . $table . '.', 1794931202);
                }
                foreach ($record as $field => $value) {
                    if (!is_int($value) && !is_string($value) && $value !== null && !$value instanceof DateTimeInterface) {
                        throw new UnexpectedValueException('The data set ' . $dataSetFile . ' contains an invalid value in ' . $table . '.' . $field . '.', 1794931203);
                    }
                    $validDataSet[$table][$recordKey][$field] = $value;
                }
            }
        }

        return $validDataSet;
    }
}
