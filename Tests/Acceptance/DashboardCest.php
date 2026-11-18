<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Acceptance;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Tests\Acceptance\Support\Tester;
use PHPUnit\Framework\Assert;

/**
 * The dashboard of the backend user shows all widgets of EXT:cart, see
 * Tests/Acceptance/Fixtures/DashboardDatabase.php for the dashboard and
 * Tests/Acceptance/Fixtures/OrdersDatabase.php for the orders.
 */
class DashboardCest
{
    private const ORDERS_PER_DAY = 'typo3-dashboard-widget[identifier="orders-per-day"]';

    private const TURNOVER_PER_DAY = 'typo3-dashboard-widget[identifier="turnover-per-day"]';

    private const PAYMENT_PAID_SHIPPING_OPEN = 'typo3-dashboard-widget[identifier="payment-paid-shipping-open"]';

    private const ORDERS_TODAY = 'typo3-dashboard-widget[identifier="orders-today"]';

    private const TURNOVER_TODAY = 'typo3-dashboard-widget[identifier="turnover-today"]';

    public function _before(Tester $I): void
    {
        $I->loginAsAdmin();
        $I->amOnUrl('http://127.0.0.1:8080/typo3/module/dashboard');
        $I->switchToIFrame('#typo3-contentIframe');
    }

    public function ordersPerDayShowsPaidAndNotShippedOrdersOfTheLast28Days(Tester $I): void
    {
        $I->waitForText('Orders per Day', 10, self::ORDERS_PER_DAY . ' .widget-title');

        $data = self::grabChartData($I, self::ORDERS_PER_DAY);

        $expected = array_fill(0, 29, 0);
        $expected[18] = 1;
        $expected[27] = 1;
        Assert::assertEquals($expected, $data);
    }

    public function turnoverPerDayShowsTheGrossTotalOfTheLast28Days(Tester $I): void
    {
        $I->waitForText('Turnover per Day', 10, self::TURNOVER_PER_DAY . ' .widget-title');

        $data = self::grabChartData($I, self::TURNOVER_PER_DAY);

        $expected = array_fill(0, 29, 0);
        $expected[8] = 49.9;
        $expected[18] = 119;
        $expected[25] = 25.5;
        $expected[27] = 89.95;
        $expected[28] = 15;
        Assert::assertEquals($expected, $data);
    }

    public function paymentPaidShippingOpenListsPaidAndNotShippedOrders(Tester $I): void
    {
        $I->waitForText('Latest Orders (paid, not shipped)', 10, self::PAYMENT_PAID_SHIPPING_OPEN . ' .widget-title');
        $I->waitForElement(self::PAYMENT_PAID_SHIPPING_OPEN . ' table', 10);

        $I->see('O-1004', self::PAYMENT_PAID_SHIPPING_OPEN . ' tbody tr:nth-child(1)');
        $I->see(date('Y-m-d', (int) strtotime('-1 day')), self::PAYMENT_PAID_SHIPPING_OPEN . ' tbody tr:nth-child(1)');
        $I->see('Payment 4', self::PAYMENT_PAID_SHIPPING_OPEN . ' tbody tr:nth-child(1)');
        $I->see('Shipping 4', self::PAYMENT_PAID_SHIPPING_OPEN . ' tbody tr:nth-child(1)');
        $I->see('O-1002', self::PAYMENT_PAID_SHIPPING_OPEN . ' tbody tr:nth-child(2)');
        $I->seeNumberOfElements(self::PAYMENT_PAID_SHIPPING_OPEN . ' tbody tr', 2);
    }

    public function paymentPaidShippingOpenLinksToTheOrder(Tester $I): void
    {
        $I->waitForElement(self::PAYMENT_PAID_SHIPPING_OPEN . ' tbody tr:nth-child(1) a', 10);

        $I->click(self::PAYMENT_PAID_SHIPPING_OPEN . ' tbody tr:nth-child(1) a');

        $I->waitForText('Order view for #O-1004', 10, 'h1');
        $I->see('Payment 4');
        $I->see('Shipping 4');
    }

    public function ordersTodayShowsTheNumberOfOrdersOfToday(Tester $I): void
    {
        $I->waitForText('Orders Today', 10, self::ORDERS_TODAY . ' .widget-title');

        $I->waitForText('1', 10, self::ORDERS_TODAY . ' .widget-number-number');
        $number = $I->grabTextFrom(self::ORDERS_TODAY . ' .widget-number-number');
        Assert::assertIsString($number);
        Assert::assertSame('1', trim($number));
    }

    public function turnoverTodayShowsTheGrossTotalOfTodayWithCurrency(Tester $I): void
    {
        $I->waitForText('Turnover Today', 10, self::TURNOVER_TODAY . ' .widget-title');

        $I->waitForText('15,00 €', 10, self::TURNOVER_TODAY . ' .widget-number-number');
    }

    public function widgetsRenderTheirTitleOnlyInTheHeader(Tester $I): void
    {
        $I->waitForElement(self::PAYMENT_PAID_SHIPPING_OPEN . ' table', 10);
        $I->waitForElement(self::TURNOVER_TODAY . ' .widget-number-number', 10);

        $I->seeNumberOfElements('typo3-dashboard-widget .widget-title', 5);
        $I->dontSeeElement('typo3-dashboard-widget .widget-content-title');
    }

    /**
     * Returns the data of the first dataset of the chart in the widget.
     *
     * Scripts executed by WebDriver run in a sandbox with modules of their
     * own, where Chart.getChart() does not know the charts of the page. So a
     * module script is added to the page (with the nonce of the content
     * security policy), which writes the data into an attribute of the canvas.
     *
     * @return array<mixed>
     */
    private static function grabChartData(Tester $I, string $widget): array
    {
        $canvas = $widget . ' canvas';
        $I->waitForElement($canvas, 10);

        $I->executeJS(
            <<<'JS'
                const [selector] = arguments;
                const script = document.createElement('script');
                script.type = 'module';
                script.nonce = document.querySelector('script[nonce]')?.nonce ?? '';
                script.textContent = `
                    import { Chart } from 'chart.js';
                    const canvas = document.querySelector(${JSON.stringify(selector)});
                    canvas.dataset.chartData = JSON.stringify(Chart.getChart(canvas)?.data.datasets[0].data ?? null);
                `;
                document.head.append(script);
            JS,
            [$canvas]
        );
        $I->waitForElement($canvas . '[data-chart-data]', 5);

        $json = $I->grabAttributeFrom($canvas, 'data-chart-data');
        Assert::assertIsString($json);

        $data = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

        Assert::assertIsArray($data, 'No chart found in ' . $widget);

        return $data;
    }
}
