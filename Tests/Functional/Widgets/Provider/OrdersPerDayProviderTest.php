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
use Extcode\Cart\Widgets\Provider\OrdersPerDayProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Localization\LanguageServiceFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

#[CoversClass(OrdersPerDayProvider::class)]
class OrdersPerDayProviderTest extends FunctionalTestCase
{
    use TestingFramework;

    private const ORDER_STORAGE_PID = 105;

    private const FILTER_PAYMENT_PAID_SHIPPING_OPEN = [
        'filter' => [
            'payment' => [
                'status' => 'paid',
            ],
            'shipping' => [
                'status' => 'open',
            ],
        ],
    ];

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

        $this->createOrder(1, 'today 12:00', 'paid', 'open');
        $this->createOrder(2, 'today 12:00', 'open', 'open');
        $this->createOrder(3, 'today 12:00', 'paid', 'shipped');
        $this->createOrder(4, '-1 day 12:00', 'paid', 'open');
        $this->createOrder(5, '-28 days 12:00', 'paid', 'open');
        $this->createOrder(6, '-29 days 12:00', 'paid', 'open');
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
        $chartData = $this->createProvider(self::FILTER_PAYMENT_PAID_SHIPPING_OPEN)->getChartData();

        self::assertIsArray($chartData['labels']);
        self::assertCount(29, $chartData['labels']);
        self::assertSame(date('Y-m-d'), $chartData['labels'][28]);
        self::assertCount(29, self::getData($chartData));
    }

    #[Test]
    public function getChartDataCountsOnlyOrdersMatchingTheConfiguredStatusFilter(): void
    {
        $data = self::getData($this->createProvider(self::FILTER_PAYMENT_PAID_SHIPPING_OPEN)->getChartData());

        $expected = array_fill(0, 29, 0);
        $expected[0] = 1;
        $expected[27] = 1;
        $expected[28] = 1;

        self::assertSame($expected, $data);
    }

    #[Test]
    public function getChartDataCountsAllOrdersWithoutStatusFilter(): void
    {
        $data = self::getData($this->createProvider([])->getChartData());

        $expected = array_fill(0, 29, 0);
        $expected[0] = 1;
        $expected[27] = 1;
        $expected[28] = 3;

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

    /**
     * @param array<string, mixed> $options
     */
    private function createProvider(array $options): OrdersPerDayProvider
    {
        return new OrdersPerDayProvider(
            $this->getConnectionPool()->getQueryBuilderForTable('tx_cart_domain_model_order_item'),
            $options
        );
    }

    private function createOrder(int $uid, string $orderDate, string $paymentStatus, string $shippingStatus): void
    {
        $connectionPool = $this->getConnectionPool();

        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_item')->insert(
            'tx_cart_domain_model_order_item',
            [
                'uid' => $uid,
                'pid' => self::ORDER_STORAGE_PID,
                'order_number' => 'O-' . $uid,
                'order_date' => strtotime($orderDate),
            ]
        );
        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_payment')->insert(
            'tx_cart_domain_model_order_payment',
            [
                'pid' => self::ORDER_STORAGE_PID,
                'item' => $uid,
                'status' => $paymentStatus,
            ]
        );
        $connectionPool->getConnectionForTable('tx_cart_domain_model_order_shipping')->insert(
            'tx_cart_domain_model_order_shipping',
            [
                'pid' => self::ORDER_STORAGE_PID,
                'item' => $uid,
                'status' => $shippingStatus,
            ]
        );
    }
}
