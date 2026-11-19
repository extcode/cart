<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;
use Extcode\Cart\Domain\Model\Order\Change\OrderChange;
use Extcode\Cart\Domain\Model\Order\Change\OrderChangeSource;
use Extcode\Cart\Domain\Model\Order\Change\OrderChangeType;
use Extcode\Cart\Domain\Model\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Model\Order\Status\ShippingStatus;
use Extcode\Cart\Domain\Repository\Order\OrderChangeRepository;
use PHPUnit\Framework\Attributes\Test;

/**
 * Changes the payment and the shipping status with the forms of the order
 * details in the backend module, like a click of an editor does.
 */
final class StatusChangeTest extends AbstractOrderStatusTestCase
{
    private const PAYMENT_UPDATE_ROUTE = 'cart_cart_orders.Backend\Order\Payment_update';

    private const SHIPPING_UPDATE_ROUTE = 'cart_cart_orders.Backend\Order\Shipping_update';

    #[Test]
    public function thePaymentFormChangesThePaymentStatus(): void
    {
        $this->createOrder(1, PaymentStatus::OPEN, ShippingStatus::OPEN);

        $response = self::submitForm(
            (string) self::dispatchShowAction(1)->getBody(),
            'paymentStatus',
            self::PAYMENT_UPDATE_ROUTE,
            ['status' => PaymentStatus::PAID]
        );

        self::assertSame(303, $response->getStatusCode(), 'The form must redirect to the order details.');
        self::assertSame(PaymentStatus::PAID, $this->getStoredStatus('tx_cart_domain_model_order_payment', 1));
        self::assertSame(ShippingStatus::OPEN, $this->getStoredStatus('tx_cart_domain_model_order_shipping', 1));

        $html = (string) self::dispatchShowAction(1)->getBody();
        self::assertStringContainsString('The payment was successfully updated.', $html);
        self::assertSame(PaymentStatus::PAID, self::getSelectedOptionOfSelect($html, 'status', 'paymentStatus'));
    }

    #[Test]
    public function theShippingFormChangesTheShippingStatus(): void
    {
        $this->createOrder(1, PaymentStatus::PAID, ShippingStatus::OPEN);

        self::submitForm(
            (string) self::dispatchShowAction(1)->getBody(),
            'shippingStatus',
            self::SHIPPING_UPDATE_ROUTE,
            ['status' => ShippingStatus::SHIPPED]
        );

        self::assertSame(ShippingStatus::SHIPPED, $this->getStoredStatus('tx_cart_domain_model_order_shipping', 1));
        self::assertSame(PaymentStatus::PAID, $this->getStoredStatus('tx_cart_domain_model_order_payment', 1));
        self::assertStringContainsString(
            'The shipping was successfully updated.',
            (string) self::dispatchShowAction(1)->getBody()
        );
    }

    #[Test]
    public function aFormOfAnOutdatedPageDoesNotChangeTheStatusChangedInTheMeantime(): void
    {
        $this->createOrder(1, PaymentStatus::OPEN, ShippingStatus::OPEN);
        // e.g. two tabs with the order details
        $firstTab = (string) self::dispatchShowAction(1)->getBody();
        $secondTab = (string) self::dispatchShowAction(1)->getBody();

        self::submitForm($firstTab, 'paymentStatus', self::PAYMENT_UPDATE_ROUTE, ['status' => PaymentStatus::PAID]);
        self::submitForm($secondTab, 'paymentStatus', self::PAYMENT_UPDATE_ROUTE, ['status' => PaymentStatus::CANCELED]);

        self::assertSame(PaymentStatus::PAID, $this->getStoredStatus('tx_cart_domain_model_order_payment', 1));
        self::assertStringContainsString(
            'The payment status was changed in the meantime, e.g. in another tab. Nothing was changed, please check the current status.',
            (string) self::dispatchShowAction(1)->getBody()
        );
        self::assertCount(1, $this->getOrderChangeRepository()->findByOrderItem(1));
    }

    #[Test]
    public function aStatusWhichIsNotRegisteredIsNotSet(): void
    {
        $this->createOrder(1, PaymentStatus::OPEN, ShippingStatus::OPEN);

        self::submitForm(
            (string) self::dispatchShowAction(1)->getBody(),
            'paymentStatus',
            self::PAYMENT_UPDATE_ROUTE,
            ['status' => 'not_registered']
        );

        self::assertSame(PaymentStatus::OPEN, $this->getStoredStatus('tx_cart_domain_model_order_payment', 1));
        self::assertStringContainsString(
            'The payment status "not_registered" is not available, nothing was changed.',
            (string) self::dispatchShowAction(1)->getBody()
        );
    }

    #[Test]
    public function submittingTheShownStatusChangesNothing(): void
    {
        $this->createOrder(1, PaymentStatus::OPEN, ShippingStatus::OPEN);

        self::submitForm(
            (string) self::dispatchShowAction(1)->getBody(),
            'paymentStatus',
            self::PAYMENT_UPDATE_ROUTE,
            ['status' => PaymentStatus::OPEN]
        );

        self::assertStringContainsString(
            'The payment has this status already, nothing was changed.',
            (string) self::dispatchShowAction(1)->getBody()
        );
        self::assertSame([], $this->getOrderChangeRepository()->findByOrderItem(1));
    }

    #[Test]
    public function theOrderDetailsShowTheChangesOfTheOrderTheLatestFirst(): void
    {
        $this->createOrder(1, PaymentStatus::OPEN, ShippingStatus::OPEN);
        $this->getOrderChangeRepository()->add(
            new OrderChange(
                orderItem: 1,
                type: OrderChangeType::PAYMENT_STATUS,
                oldValue: PaymentStatus::OPEN,
                newValue: PaymentStatus::PAID,
                date: new DateTimeImmutable('2026-11-18 09:15:00'),
                source: OrderChangeSource::CLI,
            )
        );
        self::submitForm(
            (string) self::dispatchShowAction(1)->getBody(),
            'shippingStatus',
            self::SHIPPING_UPDATE_ROUTE,
            ['status' => ShippingStatus::IN_PROCESS]
        );

        $html = self::normalizeWhitespace((string) self::dispatchShowAction(1)->getBody());

        preg_match_all('/<tr> <td>[^<]*<\/td> <td>([^<]*)<\/td> <td>([^<]*)<\/td> <td>([^<]*)<\/td> <td>([^<]*)<\/td> <\/tr>/', $html, $rowMatches, PREG_SET_ORDER);
        self::assertSame(
            [
                ['Shipping status', 'open', 'in process', 'admin'],
                ['Payment status', 'open', 'paid', 'Command line'],
            ],
            array_map(static fn (array $match): array => array_slice($match, 1), $rowMatches)
        );
        self::assertStringContainsString('<td>2026-11-18 09:15</td>', $html, 'The date of a change is rendered with time.');
    }

    #[Test]
    public function theOrderDetailsShowNoOrderHistoryWithoutChanges(): void
    {
        $this->createOrder(1, PaymentStatus::OPEN, ShippingStatus::OPEN);

        self::assertStringNotContainsString(
            'Order history',
            (string) self::dispatchShowAction(1)->getBody()
        );
    }

    private function getOrderChangeRepository(): OrderChangeRepository
    {
        $orderChangeRepository = $this->get(OrderChangeRepository::class);
        self::assertInstanceOf(OrderChangeRepository::class, $orderChangeRepository);

        return $orderChangeRepository;
    }

    private function getStoredStatus(string $table, int $uid): mixed
    {
        $row = $this->getConnectionPool()->getConnectionForTable($table)
            ->select(['status'], $table, ['uid' => $uid])
            ->fetchAssociative()
        ;

        return is_array($row) ? $row['status'] : null;
    }
}
