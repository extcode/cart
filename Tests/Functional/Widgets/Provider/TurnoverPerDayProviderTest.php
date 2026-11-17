<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Widgets\Provider;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Tests\Functional\Widgets\AbstractWidgetTestCase;
use Extcode\Cart\Widgets\Provider\TurnoverPerDayProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(TurnoverPerDayProvider::class)]
class TurnoverPerDayProviderTest extends AbstractWidgetTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createOrder(1, 'today 12:00', totalGross: 10.5);
        $this->createOrder(2, 'today 12:00', totalGross: 20.25);
        $this->createOrder(3, '-1 day 12:00', totalGross: 5.0);
        $this->createOrder(4, '-28 days 12:00', totalGross: 1.75);
        $this->createOrder(5, '-29 days 12:00', totalGross: 100.0);
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

    #[Test]
    public function getChartDataSumsOnlyOrdersMatchingTheConfiguredStatusFilter(): void
    {
        $this->createOrder(6, 'today 13:00', 'canceled', 'open', 1000.0);

        $data = self::getData($this->createProvider([
            'days' => 0,
            'filter' => ['payment' => ['status' => 'paid']],
        ])->getChartData());

        self::assertSame([30.75], $data);
    }

    #[Test]
    public function getChartDataSumsTheConfiguredField(): void
    {
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_item')->update(
            'tx_cart_domain_model_order_item',
            ['total_net' => 8.4],
            ['uid' => 1]
        );

        $data = self::getData($this->createProvider(['days' => 0, 'sum' => 'total_net'])->getChartData());

        self::assertSame([8.4], $data);
    }

    #[Test]
    public function getOrderItemsInPeriodSumsTheGrossTotalOfThePeriod(): void
    {
        $turnover = $this->createProvider()->getOrderItemsInPeriod(
            (int) strtotime('-1 day 0:00'),
            (int) strtotime('today 23:59:59')
        );

        self::assertSame(35.75, $turnover);
    }

    /**
     * @param array<string, mixed> $options
     */
    private function createProvider(array $options = []): TurnoverPerDayProvider
    {
        return new TurnoverPerDayProvider(
            $this->createOrderItemQuery(),
            $this->createStoragePidAccess(),
            $options
        );
    }
}
