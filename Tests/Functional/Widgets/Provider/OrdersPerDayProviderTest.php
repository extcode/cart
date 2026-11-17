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
use Extcode\Cart\Widgets\Provider\OrdersPerDayProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(OrdersPerDayProvider::class)]
class OrdersPerDayProviderTest extends AbstractWidgetTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createOrder(1, 'today 12:00', 'paid', 'open');
        $this->createOrder(2, 'today 12:00', 'open', 'open');
        $this->createOrder(3, 'today 12:00', 'paid', 'shipped');
        $this->createOrder(4, '-1 day 12:00', 'paid', 'open');
        $this->createOrder(5, '-28 days 12:00', 'paid', 'open');
        $this->createOrder(6, '-29 days 12:00', 'paid', 'open');
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
     * @param array<string, mixed> $options
     */
    private function createProvider(array $options): OrdersPerDayProvider
    {
        return new OrdersPerDayProvider(
            $this->getConnectionPool()->getQueryBuilderForTable('tx_cart_domain_model_order_item'),
            $options
        );
    }
}
