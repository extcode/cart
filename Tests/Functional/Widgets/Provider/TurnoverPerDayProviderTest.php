<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Widgets\Provider;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Codappix\Typo3PhpDatasets\TestingFramework;
use Extcode\Cart\Widgets\Provider\TurnoverPerDayProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(TurnoverPerDayProvider::class)]
class TurnoverPerDayProviderTest extends FunctionalTestCase
{
    use TestingFramework;

    private const ORDER_STORAGE_PID = 105;

    protected function setUp(): void
    {
        $this->testExtensionsToLoad = [
            'extcode/cart',
        ];

        $this->coreExtensionsToLoad = [
            'typo3/cms-dashboard',
        ];

        parent::setUp();

        $this->importPHPDataSet(__DIR__ . '/../../../Fixtures/BaseDatabase.php');
        $this->importPHPDataSet(__DIR__ . '/../../../Fixtures/BackendUser.php');

        $this->setUpBackendUser(1);

        $GLOBALS['LANG'] = GeneralUtility::makeInstance(LanguageServiceFactory::class)->create('en');

        $this->createOrder(1, 'today 12:00', 10.5);
        $this->createOrder(2, 'today 12:00', 20.25);
        $this->createOrder(3, '-1 day 12:00', 5.0);
        $this->createOrder(4, '-28 days 12:00', 1.75);
        $this->createOrder(5, '-29 days 12:00', 100.0);
    }

    protected function tearDown(): void
    {
        unset(
            $GLOBALS['LANG']
        );

        parent::tearDown();
    }

    #[Test]
    public function getChartDataReturnsOneLabelAndValueForEachDayOfThePeriod(): void
    {
        $chartData = $this->createProvider()->getChartData();

        self::assertIsArray($chartData['labels']);
        self::assertCount(29, $chartData['labels']);
        self::assertSame(date('Y-m-d'), $chartData['labels'][28]);
        self::assertCount(29, self::getData($chartData));
    }

    #[Test]
    public function getChartDataSumsTheGrossTotalOfTheOrdersPerDay(): void
    {
        $data = self::getData($this->createProvider()->getChartData());

        $expected = array_fill(0, 29, 0.0);
        $expected[0] = 1.75;
        $expected[27] = 5.0;
        $expected[28] = 30.75;

        self::assertSame($expected, $data);
    }

    /**
     * @param array<mixed> $chartData
     *
     * @return array<mixed>
     */
    private static function getData(array $chartData): array
    {
        self::assertIsArray($chartData['datasets']);
        self::assertIsArray($chartData['datasets'][0]);
        self::assertIsArray($chartData['datasets'][0]['data']);

        return $chartData['datasets'][0]['data'];
    }

    private function createProvider(): TurnoverPerDayProvider
    {
        return new TurnoverPerDayProvider(
            $this->getConnectionPool()->getQueryBuilderForTable('tx_cart_domain_model_order_item'),
            [
                'sum' => 'total_gross',
            ]
        );
    }

    private function createOrder(int $uid, string $orderDate, float $totalGross): void
    {
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_item')->insert(
            'tx_cart_domain_model_order_item',
            [
                'uid' => $uid,
                'pid' => self::ORDER_STORAGE_PID,
                'order_number' => 'O-' . $uid,
                'order_date' => strtotime($orderDate),
                'total_gross' => $totalGross,
            ]
        );
    }
}
