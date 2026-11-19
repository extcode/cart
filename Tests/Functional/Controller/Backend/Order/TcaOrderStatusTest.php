<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Model\Order\Status\ShippingStatus;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
use PHPUnit\Framework\Attributes\Test;

/**
 * The orders backend module with own status added to the items of the TCA
 * like until cart 12.3, see
 * Tests/Fixtures/cart_order_status_tca/Configuration/TCA/Overrides/.
 *
 * TODO: 13.0.0 Remove with the status in the items of the TCA and cart_order_status_tca.
 */
#[IgnoreDeprecations]
final class TcaOrderStatusTest extends AbstractOrderStatusTestCase
{
    private const EXPECTED_PAYMENT_STATUS = [
        PaymentStatus::OPEN => 'open',
        PaymentStatus::PENDING => 'pending',
        PaymentStatus::PAID => 'paid',
        PaymentStatus::CANCELED => 'canceled',
        'refunded_via_tca' => 'Refunded via TCA',
    ];

    private const EXPECTED_SHIPPING_STATUS = [
        ShippingStatus::OPEN => 'open',
        ShippingStatus::ON_HOLD => 'on hold',
        ShippingStatus::IN_PROCESS => 'in process',
        ShippingStatus::SHIPPED => 'shipped',
        'delivered_via_tca' => 'Delivered via TCA',
    ];

    protected array $orderStatusExtensionsToLoad = [
        'typo3conf/ext/cart/Tests/Fixtures/cart_order_status_tca',
    ];

    #[Test]
    public function listActionRendersTheStatusOfTheTcaAfterTheRegisteredStatusInTheFilter(): void
    {
        $this->expectDeprecationsOfTheStatusOfTheTca();
        // the filter is only rendered if there are orders
        $this->createOrder(1, PaymentStatus::PAID, ShippingStatus::OPEN);

        $html = (string) self::dispatchListAction()->getBody();

        self::assertSame(
            ['' => 'all'] + self::EXPECTED_PAYMENT_STATUS,
            self::getOptionsOfSelect($html, 'search[paymentStatus]')
        );
        self::assertSame(
            ['' => 'all'] + self::EXPECTED_SHIPPING_STATUS,
            self::getOptionsOfSelect($html, 'search[shippingStatus]')
        );
    }

    #[Test]
    public function listActionRendersTheLabelOfTheStatusOfTheTcaOfAnOrder(): void
    {
        $this->expectDeprecationsOfTheStatusOfTheTca();
        $this->createOrder(1, 'refunded_via_tca', 'delivered_via_tca');

        $html = self::normalizeWhitespace((string) self::dispatchListAction()->getBody());

        self::assertStringContainsString(
            '<td class="order-payment-status-refunded_via_tca"> Payment 1 <br/> (&nbsp;Refunded via TCA&nbsp;) </td>',
            $html
        );
        self::assertStringContainsString(
            '<td class="order-shipping-status-delivered_via_tca"> Shipping 1 <br/> (&nbsp;Delivered via TCA&nbsp;) </td>',
            $html
        );
    }

    #[Test]
    public function showActionRendersTheStatusOfTheTcaAfterTheRegisteredStatusAndSelectsTheStatusOfTheOrder(): void
    {
        $this->expectDeprecationsOfTheStatusOfTheTca();
        $this->createOrder(1, 'refunded_via_tca', 'delivered_via_tca');

        $html = (string) self::dispatchShowAction(1)->getBody();

        self::assertSame(self::EXPECTED_PAYMENT_STATUS, self::getOptionsOfSelect($html, 'status', 'paymentStatus'));
        self::assertSame('refunded_via_tca', self::getSelectedOptionOfSelect($html, 'status', 'paymentStatus'));

        self::assertSame(self::EXPECTED_SHIPPING_STATUS, self::getOptionsOfSelect($html, 'status', 'shippingStatus'));
        self::assertSame('delivered_via_tca', self::getSelectedOptionOfSelect($html, 'status', 'shippingStatus'));
    }

    private function expectDeprecationsOfTheStatusOfTheTca(): void
    {
        $this->expectUserDeprecationMessage(
            'Adding the payment status "refunded_via_tca" to'
            . ' $GLOBALS[\'TCA\'][\'tx_cart_domain_model_order_payment\'][\'columns\'][\'status\'][\'config\'][\'items\'] is deprecated'
            . ' since cart 12.4 and will stop working in cart 13.0.'
            . ' Register the status in Configuration/OrderStatus.php of your extension instead.'
        );
        $this->expectUserDeprecationMessage(
            'Adding the shipping status "delivered_via_tca" to'
            . ' $GLOBALS[\'TCA\'][\'tx_cart_domain_model_order_shipping\'][\'columns\'][\'status\'][\'config\'][\'items\'] is deprecated'
            . ' since cart 12.4 and will stop working in cart 13.0.'
            . ' Register the status in Configuration/OrderStatus.php of your extension instead.'
        );
    }
}
