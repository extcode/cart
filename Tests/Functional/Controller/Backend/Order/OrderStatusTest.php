<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * The orders backend module with the payment and shipping status of cart,
 * see Configuration/OrderStatus.php.
 */
final class OrderStatusTest extends AbstractOrderStatusTestCase
{
    private const EXPECTED_PAYMENT_STATUS = [
        PaymentStatus::OPEN => 'open',
        PaymentStatus::PENDING => 'pending',
        PaymentStatus::PAID => 'paid',
        PaymentStatus::CANCELED => 'canceled',
    ];

    private const EXPECTED_SHIPPING_STATUS = [
        ShippingStatus::OPEN => 'open',
        ShippingStatus::ON_HOLD => 'on hold',
        ShippingStatus::IN_PROCESS => 'in process',
        ShippingStatus::SHIPPED => 'shipped',
    ];

    #[Test]
    public function listActionRendersAllStatusInTheirOrderInTheFilter(): void
    {
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

    #[DataProvider('statusDataProvider')]
    #[Test]
    public function listActionRendersTheLabelOfTheStatusOfAnOrder(string $paymentStatus, string $shippingStatus): void
    {
        $this->createOrder(1, $paymentStatus, $shippingStatus);

        $html = self::normalizeWhitespace((string) self::dispatchListAction()->getBody());

        self::assertStringContainsString(
            '<td class="order-payment-status-' . $paymentStatus . '"> Payment 1 <br/> (&nbsp;' . self::EXPECTED_PAYMENT_STATUS[$paymentStatus] . '&nbsp;) </td>',
            $html
        );
        self::assertStringContainsString(
            '<td class="order-shipping-status-' . $shippingStatus . '"> Shipping 1 <br/> (&nbsp;' . self::EXPECTED_SHIPPING_STATUS[$shippingStatus] . '&nbsp;) </td>',
            $html
        );
    }

    #[DataProvider('statusDataProvider')]
    #[Test]
    public function showActionRendersAllStatusInTheirOrderAndSelectsTheStatusOfTheOrder(string $paymentStatus, string $shippingStatus): void
    {
        $this->createOrder(1, $paymentStatus, $shippingStatus);

        $html = (string) self::dispatchShowAction(1)->getBody();

        self::assertSame(self::EXPECTED_PAYMENT_STATUS, self::getOptionsOfSelect($html, 'status', 'paymentStatus'));
        self::assertSame($paymentStatus, self::getSelectedOptionOfSelect($html, 'status', 'paymentStatus'));

        self::assertSame(self::EXPECTED_SHIPPING_STATUS, self::getOptionsOfSelect($html, 'status', 'shippingStatus'));
        self::assertSame($shippingStatus, self::getSelectedOptionOfSelect($html, 'status', 'shippingStatus'));
    }

    /**
     * @return iterable<string, array{paymentStatus: string, shippingStatus: string}>
     */
    public static function statusDataProvider(): iterable
    {
        yield 'first status' => [
            'paymentStatus' => PaymentStatus::OPEN,
            'shippingStatus' => ShippingStatus::OPEN,
        ];
        yield 'status in between' => [
            'paymentStatus' => PaymentStatus::PENDING,
            'shippingStatus' => ShippingStatus::IN_PROCESS,
        ];
        yield 'last status' => [
            'paymentStatus' => PaymentStatus::CANCELED,
            'shippingStatus' => ShippingStatus::SHIPPED,
        ];
    }

    #[Test]
    public function listActionFiltersTheOrdersByTheStatus(): void
    {
        $this->createOrder(1, PaymentStatus::PAID, ShippingStatus::OPEN);
        $this->createOrder(2, PaymentStatus::PENDING, ShippingStatus::ON_HOLD);
        $this->createOrder(3, PaymentStatus::CANCELED, ShippingStatus::SHIPPED);

        self::assertSame(
            ['O-002'],
            self::getRenderedOrderNumbers((string) self::dispatchListAction(['paymentStatus' => PaymentStatus::PENDING])->getBody())
        );
        self::assertSame(
            ['O-003'],
            self::getRenderedOrderNumbers((string) self::dispatchListAction(['paymentStatus' => '', 'shippingStatus' => ShippingStatus::SHIPPED])->getBody())
        );
    }
}
