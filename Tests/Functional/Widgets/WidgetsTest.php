<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Widgets;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Widgets\PaymentPaidShippingOpen;
use Generator;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Routing\Router;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\NormalizedParams;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Dashboard\WidgetRegistry;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;

#[CoversClass(PaymentPaidShippingOpen::class)]
class WidgetsTest extends AbstractWidgetTestCase
{
    protected function tearDown(): void
    {
        unset(
            $GLOBALS['TYPO3_REQUEST']
        );

        parent::tearDown();
    }

    /**
     * @param class-string $widgetClass
     */
    #[DataProvider('widgetDataProvider')]
    #[Test]
    public function widgetIsRegisteredInWidgetGroupCart(string $identifier, string $widgetClass, string $titleKey): void
    {
        $widgetRegistry = $this->get(WidgetRegistry::class);
        self::assertInstanceOf(WidgetRegistry::class, $widgetRegistry);

        $widgetConfigurations = $widgetRegistry->getAvailableWidgetsForWidgetGroup('cart');
        self::assertArrayHasKey($identifier, $widgetConfigurations);
        self::assertSame(
            'LLL:EXT:cart/Resources/Private/Language/locallang_be.xlf:' . $titleKey,
            $widgetConfigurations[$identifier]->getTitle()
        );

        self::assertInstanceOf(
            $widgetClass,
            $widgetRegistry->getAvailableWidget(self::createDashboardRequest(), $identifier)
        );
    }

    /**
     * @return Generator<string, array{string, class-string, string}>
     */
    public static function widgetDataProvider(): iterable
    {
        yield 'OrdersPerDay' => ['OrdersPerDay', BarChartWidget::class, 'dashboard.widgets.orders_per_day.title'];
        yield 'TurnoverPerDay' => ['TurnoverPerDay', BarChartWidget::class, 'dashboard.widgets.turnover_per_day.title'];
        yield 'PaymentPaidShippingOpen' => ['PaymentPaidShippingOpen', PaymentPaidShippingOpen::class, 'dashboard.widgets.payment_paid_shipping_open.title'];
    }

    #[Test]
    public function paymentPaidShippingOpenRendersOrdersWithPaymentPaidAndShippingOpen(): void
    {
        $this->createOrder(1, '-1 day 12:00', 'paid', 'open');
        $this->createOrder(2, 'today 12:00', 'paid', 'shipped');

        $content = $this->renderPaymentPaidShippingOpen();

        self::assertStringContainsString('O-1', $content);
        self::assertStringContainsString('Payment 1', $content);
        self::assertStringContainsString('Shipping 1', $content);
        self::assertStringContainsString(date('Y-m-d', (int)strtotime('-1 day 12:00')), $content);
        self::assertStringContainsString('&amp;orderItem=1', $content);
        self::assertStringNotContainsString('O-2', $content);
    }

    private function renderPaymentPaidShippingOpen(): string
    {
        $widgetRegistry = $this->get(WidgetRegistry::class);
        self::assertInstanceOf(WidgetRegistry::class, $widgetRegistry);

        $widget = $widgetRegistry->getAvailableWidget(self::createDashboardRequest(), 'PaymentPaidShippingOpen');
        self::assertInstanceOf(PaymentPaidShippingOpen::class, $widget);

        return $widget->renderWidgetContent();
    }

    private static function createDashboardRequest(): ServerRequestInterface
    {
        $route = GeneralUtility::makeInstance(Router::class)->getRoute('dashboard');
        self::assertNotNull($route);

        $request = (new ServerRequest('https://localhost/typo3' . $route->getPath(), 'GET'))
            ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_BE)
            ->withAttribute('route', $route)
        ;
        $request = $request->withAttribute('normalizedParams', NormalizedParams::createFromRequest($request));
        $GLOBALS['TYPO3_REQUEST'] = $request;

        return $request;
    }
}
