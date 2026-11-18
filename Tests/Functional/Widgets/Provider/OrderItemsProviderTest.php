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
use Extcode\Cart\Widgets\Provider\OrderItemsProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(OrderItemsProvider::class)]
class OrderItemsProviderTest extends AbstractWidgetTestCase
{
    #[Test]
    public function getItemsReturnsOnlyOrdersMatchingTheConfiguredStatusFilter(): void
    {
        $this->createOrder(1, '-3 days 12:00', 'paid', 'open');
        $this->createOrder(2, '-2 days 12:00', 'open', 'open');
        $this->createOrder(3, '-1 day 12:00', 'paid', 'shipped');
        $this->createOrder(4, 'today 12:00', 'paid', 'open', 89.95);

        $items = $this->createProvider(self::FILTER_PAYMENT_PAID_SHIPPING_OPEN)->getItems();

        self::assertSame([4, 1], array_column($items, 'uid'));

        // DECIMAL values are strings on MySQL/MariaDB, numbers on SQLite
        self::assertIsNumeric($items[0]['total_gross']);
        self::assertSame(89.95, (float) $items[0]['total_gross']);
        unset($items[0]['total_gross']);

        self::assertSame(
            [
                'uid' => 4,
                'pid' => self::ORDER_STORAGE_PID,
                'order_number' => 'O-4',
                'order_date' => strtotime('today 12:00'),
                'invoice_number' => '',
                'invoice_date' => 0,
                'currency_sign' => '',
                'payment_name' => 'Payment 4',
                'payment_status' => 'paid',
                'shipping_name' => 'Shipping 4',
                'shipping_status' => 'open',
            ],
            $items[0]
        );
    }

    #[Test]
    public function getItemsReturnsAllOrdersWithoutStatusFilter(): void
    {
        $this->createOrder(1, '-1 day 12:00', 'paid', 'open');
        $this->createOrder(2, 'today 12:00', 'open', 'shipped');

        $items = $this->createProvider([])->getItems();

        self::assertSame([2, 1], array_column($items, 'uid'));
    }

    #[Test]
    public function getItemsReturnsTheConfiguredNumberOfNewestOrders(): void
    {
        for ($uid = 1; $uid <= 12; $uid++) {
            $this->createOrder($uid, '-' . (12 - $uid) . ' days 12:00');
        }

        $items = $this->createProvider(self::FILTER_PAYMENT_PAID_SHIPPING_OPEN)->getItems();

        self::assertSame([12, 11, 10, 9, 8, 7, 6, 5, 4, 3], array_column($items, 'uid'));
    }

    #[Test]
    public function getItemsReturnsTheSameItemsOnRepeatedCalls(): void
    {
        $this->createOrder(1, 'today 12:00', 'paid', 'open');

        $provider = $this->createProvider(self::FILTER_PAYMENT_PAID_SHIPPING_OPEN);

        self::assertSame($provider->getItems(), $provider->getItems());
    }

    #[Test]
    public function getItemsReturnsOnlyOrdersOfTheConfiguredStorageFolders(): void
    {
        $this->createSecondStorageFolder();
        $this->createOrder(1, '-1 day 12:00', pid: self::ORDER_STORAGE_PID);
        $this->createOrder(2, 'today 12:00', pid: self::SECOND_ORDER_STORAGE_PID);

        $items = $this->createProvider(['storagePids' => [self::SECOND_ORDER_STORAGE_PID]])->getItems();

        self::assertSame([2], array_column($items, 'uid'));
    }

    #[Test]
    public function getItemsAcceptsTheStorageFoldersAsCommaSeparatedList(): void
    {
        $this->createSecondStorageFolder();
        $this->createOrder(1, '-1 day 12:00', pid: self::ORDER_STORAGE_PID);
        $this->createOrder(2, 'today 12:00', pid: self::SECOND_ORDER_STORAGE_PID);
        $this->createOrder(3, 'today 13:00', pid: 1);

        $items = $this->createProvider(['storagePids' => self::ORDER_STORAGE_PID . ',' . self::SECOND_ORDER_STORAGE_PID])->getItems();

        self::assertSame([2, 1], array_column($items, 'uid'));
    }

    #[Test]
    public function getItemsReturnsOnlyOrdersOfStorageFoldersTheBackendUserMayShow(): void
    {
        $this->createSecondStorageFolder();
        $this->createOrder(1, '-1 day 12:00', pid: self::ORDER_STORAGE_PID);
        $this->createOrder(2, 'today 12:00', pid: self::SECOND_ORDER_STORAGE_PID);
        $this->setUpEditor();

        $items = $this->createProvider([])->getItems();

        self::assertSame([2], array_column($items, 'uid'));
    }

    #[Test]
    public function getItemsReturnsNoOrdersIfTheBackendUserMayNotShowTheConfiguredStorageFolders(): void
    {
        $this->createSecondStorageFolder();
        $this->createOrder(1, '-1 day 12:00', pid: self::ORDER_STORAGE_PID);
        $this->createOrder(2, 'today 12:00', pid: self::SECOND_ORDER_STORAGE_PID);
        $this->setUpEditor();

        $items = $this->createProvider(['storagePids' => [self::ORDER_STORAGE_PID]])->getItems();

        self::assertSame([], $items);
    }

    #[Test]
    public function getItemsReturnsTheConfiguredLimitAndOrder(): void
    {
        for ($uid = 1; $uid <= 5; $uid++) {
            $this->createOrder($uid, '-' . (5 - $uid) . ' days 12:00');
        }

        $items = $this->createProvider([
            'order_by' => 'tx_cart_domain_model_order_item.order_date',
            'order_order' => 'asc',
            'limit' => 3,
        ])->getItems();

        self::assertSame([1, 2, 3], array_column($items, 'uid'));
    }

    /**
     * @param array<string, mixed> $options
     */
    private function createProvider(array $options): OrderItemsProvider
    {
        return new OrderItemsProvider(
            $this->createOrderItemQuery(),
            $this->createStoragePidAccess(),
            $options
        );
    }
}
