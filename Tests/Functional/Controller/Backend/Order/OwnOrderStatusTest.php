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
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * The orders backend module with own payment and shipping status of other
 * extensions at different positions, see
 * Tests/Fixtures/cart_order_status/Configuration/OrderStatus.php and
 * Tests/Fixtures/cart_order_status_extended/Configuration/OrderStatus.php.
 */
final class OwnOrderStatusTest extends AbstractOrderStatusTestCase
{
    private const EXPECTED_PAYMENT_STATUS = [
        'new' => 'New',
        PaymentStatus::OPEN => 'open',
        PaymentStatus::PENDING => 'Awaiting payment',
        PaymentStatus::PAID => 'paid',
        'partially_refunded' => 'Partially refunded',
        'refunded' => 'Refunded',
        PaymentStatus::CANCELED => 'canceled',
        'chargeback' => 'Chargeback',
    ];

    private const EXPECTED_SHIPPING_STATUS = [
        'ordered' => 'Ordered',
        ShippingStatus::OPEN => 'open',
        ShippingStatus::ON_HOLD => 'on hold',
        ShippingStatus::IN_PROCESS => 'in process',
        'packed' => 'Packed',
        ShippingStatus::SHIPPED => 'shipped',
        'delivered' => 'Delivered',
    ];

    protected array $orderStatusExtensionsToLoad = [
        'typo3conf/ext/cart/Tests/Fixtures/cart_order_status',
        'typo3conf/ext/cart/Tests/Fixtures/cart_order_status_extended',
    ];

    #[Test]
    public function listActionRendersAllPaymentStatusInTheirOrderInTheFilter(): void
    {
        // the filter is only rendered if there are orders
        $this->createOrder(1, PaymentStatus::PAID, ShippingStatus::OPEN);

        $html = (string) self::dispatchListAction()->getBody();

        self::assertSame(
            ['' => 'all'] + self::EXPECTED_PAYMENT_STATUS,
            self::getOptionsOfSelect($html, 'search[paymentStatus]')
        );
    }

    #[Test]
    public function listActionRendersAllShippingStatusInTheirOrderInTheFilter(): void
    {
        // the filter is only rendered if there are orders
        $this->createOrder(1, PaymentStatus::PAID, ShippingStatus::OPEN);

        $html = (string) self::dispatchListAction()->getBody();

        self::assertSame(
            ['' => 'all'] + self::EXPECTED_SHIPPING_STATUS,
            self::getOptionsOfSelect($html, 'search[shippingStatus]')
        );
    }

    #[DataProvider('ownStatusLabelDataProvider')]
    #[Test]
    public function listActionRendersTheLabelOfTheStatusOfAnOrder(
        string $paymentStatus,
        string $shippingStatus,
        string $expectedPaymentLabel,
        string $expectedShippingLabel
    ): void {
        $this->createOrder(1, $paymentStatus, $shippingStatus);

        $html = self::normalizeWhitespace((string) self::dispatchListAction()->getBody());

        self::assertStringContainsString(
            '<td class="order-payment-status-' . $paymentStatus . '"> Payment 1 <br/> (&nbsp;' . $expectedPaymentLabel . '&nbsp;) </td>',
            $html
        );
        self::assertStringContainsString(
            '<td class="order-shipping-status-' . $shippingStatus . '"> Shipping 1 <br/> (&nbsp;' . $expectedShippingLabel . '&nbsp;) </td>',
            $html
        );
    }

    /**
     * @return iterable<string, array{paymentStatus: string, shippingStatus: string, expectedPaymentLabel: string, expectedShippingLabel: string}>
     */
    public static function ownStatusLabelDataProvider(): iterable
    {
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
        yield 'own status between a status of cart and one of another extension' => [
            'paymentStatus' => 'partially_refunded',
            'shippingStatus' => 'packed',
            'expectedPaymentLabel' => 'Partially refunded',
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
            'shippingStatus' => ShippingStatus::IN_PROCESS,
            'expectedPaymentLabel' => 'Awaiting payment',
            'expectedShippingLabel' => 'in process',
        ];
    }

    #[DataProvider('ownStatusDataProvider')]
    #[Test]
    public function listActionFiltersTheOrdersByTheStatus(string $paymentStatus, string $shippingStatus): void
    {
        $this->createOrder(1, PaymentStatus::PAID, ShippingStatus::OPEN);
        $this->createOrder(2, $paymentStatus, $shippingStatus);
        $this->createOrder(3, PaymentStatus::CANCELED, ShippingStatus::SHIPPED);

        self::assertSame(
            ['O-002'],
            self::getRenderedOrderNumbers((string) self::dispatchListAction(['paymentStatus' => $paymentStatus])->getBody()),
            'Only the order with the payment status "' . $paymentStatus . '" must be listed.'
        );
        self::assertSame(
            ['O-002'],
            self::getRenderedOrderNumbers((string) self::dispatchListAction(['paymentStatus' => '', 'shippingStatus' => $shippingStatus])->getBody()),
            'Only the order with the shipping status "' . $shippingStatus . '" must be listed.'
        );
    }

    #[DataProvider('ownStatusDataProvider')]
    #[Test]
    public function showActionRendersAllStatusInTheirOrderAndSelectsTheStatusOfTheOrder(string $paymentStatus, string $shippingStatus): void
    {
        $this->createOrder(1, $paymentStatus, $shippingStatus);

        $html = (string) self::dispatchShowAction(1)->getBody();

        self::assertSame(self::EXPECTED_PAYMENT_STATUS, self::getOptionsOfSelect($html, 'payment[status]'));
        self::assertSame($paymentStatus, self::getSelectedOptionOfSelect($html, 'payment[status]'));

        self::assertSame(self::EXPECTED_SHIPPING_STATUS, self::getOptionsOfSelect($html, 'shipping[status]'));
        self::assertSame($shippingStatus, self::getSelectedOptionOfSelect($html, 'shipping[status]'));
    }

    /**
     * @return iterable<string, array{paymentStatus: string, shippingStatus: string}>
     */
    public static function ownStatusDataProvider(): iterable
    {
        foreach (self::ownStatusLabelDataProvider() as $name => $data) {
            yield $name => [
                'paymentStatus' => $data['paymentStatus'],
                'shippingStatus' => $data['shippingStatus'],
            ];
        }
    }

    #[Test]
    public function listActionRendersTheIdentifierOfAStatusWhichIsNotRegistered(): void
    {
        $this->createOrder(1, 'not_registered', ShippingStatus::OPEN);

        $html = self::normalizeWhitespace((string) self::dispatchListAction()->getBody());

        self::assertStringContainsString(
            '<td class="order-payment-status-not_registered"> Payment 1 <br/> (&nbsp;not_registered&nbsp;) </td>',
            $html
        );
    }

    #[Test]
    public function showActionKeepsAStatusWhichIsNotRegisteredAsLastOption(): void
    {
        $this->createOrder(1, 'not_registered', ShippingStatus::OPEN);

        $html = (string) self::dispatchShowAction(1)->getBody();

        self::assertSame(
            self::EXPECTED_PAYMENT_STATUS + ['not_registered' => 'not_registered'],
            self::getOptionsOfSelect($html, 'payment[status]'),
            'Otherwise saving the form would change the status of the order unnoticed.'
        );
        self::assertSame('not_registered', self::getSelectedOptionOfSelect($html, 'payment[status]'));

        self::assertSame(
            self::EXPECTED_SHIPPING_STATUS,
            self::getOptionsOfSelect($html, 'shipping[status]'),
            'A registered status of the other service must not add an option.'
        );
    }
}
