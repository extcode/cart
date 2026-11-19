<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Templates;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Domain\Model\Order\Shipping;
use Extcode\Cart\Domain\Model\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Model\Order\Status\ShippingStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Core\SystemEnvironmentBuilder;
use TYPO3\CMS\Core\Http\ServerRequest;
use TYPO3\CMS\Core\Http\Uri;
use TYPO3\CMS\Core\Site\Entity\SiteLanguage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\View\ViewFactoryData;
use TYPO3\CMS\Core\View\ViewFactoryInterface;
use TYPO3\CMS\Extbase\Mvc\ExtbaseRequestParameters;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\TestingFramework\Core\Functional\FunctionalTestCase;

/**
 * The order details in the frontend (partial Order/Show/Properties) render the
 * labels of the registered payment and shipping status, see
 * Tests/Fixtures/cart_order_status/Configuration/OrderStatus.php and
 * Tests/Fixtures/cart_order_status_extended/Configuration/OrderStatus.php.
 */
final class OrderStatusTest extends FunctionalTestCase
{
    protected array $testExtensionsToLoad = [
        'extcode/cart',
        'typo3conf/ext/cart/Tests/Fixtures/cart_order_status',
        'typo3conf/ext/cart/Tests/Fixtures/cart_order_status_extended',
    ];

    #[DataProvider('statusDataProvider')]
    #[Test]
    public function orderDetailsRenderTheLabelsOfTheStatusOfTheOrder(
        string $paymentStatus,
        string $shippingStatus,
        string $expectedPaymentLabel,
        string $expectedShippingLabel
    ): void {
        $payment = new Payment();
        $payment->setStatus($paymentStatus);
        $shipping = new Shipping();
        $shipping->setStatus($shippingStatus);
        $orderItem = new Item();
        $orderItem->setPayment($payment);
        $orderItem->setShipping($shipping);

        self::assertSame(
            [$expectedPaymentLabel, $expectedShippingLabel],
            self::getRenderedStatus(self::renderProperties($orderItem))
        );
    }

    /**
     * @return iterable<string, array{paymentStatus: string, shippingStatus: string, expectedPaymentLabel: string, expectedShippingLabel: string}>
     */
    public static function statusDataProvider(): iterable
    {
        yield 'status of cart' => [
            'paymentStatus' => PaymentStatus::PAID,
            'shippingStatus' => ShippingStatus::IN_PROCESS,
            'expectedPaymentLabel' => 'paid',
            'expectedShippingLabel' => 'in process',
        ];
        yield 'own status before the first status of cart' => [
            'paymentStatus' => 'new',
            'shippingStatus' => 'ordered',
            'expectedPaymentLabel' => 'New',
            'expectedShippingLabel' => 'Ordered',
        ];
        yield 'own status between two status of cart' => [
            'paymentStatus' => 'refunded',
            'shippingStatus' => 'packed',
            'expectedPaymentLabel' => 'Refunded',
            'expectedShippingLabel' => 'Packed',
        ];
        yield 'own status after the last status of cart' => [
            'paymentStatus' => 'chargeback',
            'shippingStatus' => 'delivered',
            'expectedPaymentLabel' => 'Chargeback',
            'expectedShippingLabel' => 'Delivered',
        ];
        yield 'status of cart with a label changed by another extension' => [
            'paymentStatus' => PaymentStatus::PENDING,
            'shippingStatus' => ShippingStatus::OPEN,
            'expectedPaymentLabel' => 'Awaiting payment',
            'expectedShippingLabel' => 'open',
        ];
        yield 'status which is not registered' => [
            'paymentStatus' => 'not_registered',
            'shippingStatus' => 'not_registered',
            'expectedPaymentLabel' => 'not_registered',
            'expectedShippingLabel' => 'not_registered',
        ];
    }

    #[Test]
    public function orderDetailsRenderNoStatusForAnOrderWithoutPaymentAndShipping(): void
    {
        self::assertSame(
            ['', ''],
            self::getRenderedStatus(self::renderProperties(new Item()))
        );
    }

    private static function renderProperties(Item $orderItem): string
    {
        $extbaseRequestParameters = new ExtbaseRequestParameters();
        $extbaseRequestParameters->setControllerExtensionName('Cart');
        $request = new Request(
            (new ServerRequest('https://example.com/'))
                ->withAttribute('applicationType', SystemEnvironmentBuilder::REQUESTTYPE_FE)
                ->withAttribute('language', new SiteLanguage(0, 'en_US.UTF-8', new Uri('/'), []))
                ->withAttribute('extbase', $extbaseRequestParameters)
        );

        $view = GeneralUtility::makeInstance(ViewFactoryInterface::class)->create(
            new ViewFactoryData(
                templateRootPaths: ['EXT:cart/Tests/Functional/Fixtures/Templates/'],
                partialRootPaths: ['EXT:cart/Resources/Private/Partials/'],
                layoutRootPaths: ['EXT:cart/Resources/Private/Layouts/'],
                request: $request,
            )
        );
        $view->assign('orderItem', $orderItem);

        return $view->render('Order/Properties');
    }

    /**
     * @return list<string> the rendered payment and the shipping status
     */
    private static function getRenderedStatus(string $html): array
    {
        preg_match('/<tr class="properties-status">(.*?)<\/tr>/s', $html, $rowMatches);
        self::assertArrayHasKey(1, $rowMatches, 'No status of the order has been rendered.');

        preg_match_all('/<td>(.*?)<\/td>/s', $rowMatches[1], $cellMatches);

        return array_map(
            static fn (string $cell): string => html_entity_decode(mb_trim($cell)),
            $cellMatches[1]
        );
    }
}
