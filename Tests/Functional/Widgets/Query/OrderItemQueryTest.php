<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Functional\Widgets\Query;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Tests\Functional\Widgets\AbstractWidgetTestCase;
use Extcode\Cart\Widgets\Query\OrderItemCriteria;
use Extcode\Cart\Widgets\Query\OrderItemQuery;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(OrderItemQuery::class)]
class OrderItemQueryTest extends AbstractWidgetTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createSecondStorageFolder();
        $this->createOrder(1, '-2 days 12:00', 'paid', 'open', 10.0);
        $this->createOrder(2, '-1 day 12:00', 'open', 'open', 20.0);
        $this->createOrder(3, 'today 12:00', 'paid', 'shipped', 30.0, self::SECOND_ORDER_STORAGE_PID);
    }

    #[Test]
    public function findValuesInPeriodReturnsDateAndValueOfTheOrdersInThePeriod(): void
    {
        $rows = $this->findValuesInPeriod(new OrderItemCriteria(), 'total_gross', '-1 day 0:00');

        self::assertSame(
            [
                ['date' => (int) strtotime('-1 day 12:00'), 'value' => 20.0],
                ['date' => (int) strtotime('today 12:00'), 'value' => 30.0],
            ],
            array_map(
                static fn (array $row): array => ['date' => $row['date'], 'value' => is_numeric($row['value']) ? (float) $row['value'] : null],
                $rows
            )
        );
    }

    #[Test]
    public function findValuesInPeriodReturnsOneAsValueWithoutValueField(): void
    {
        $rows = $this->findValuesInPeriod(new OrderItemCriteria(), null);

        self::assertEquals([1, 1, 1], array_column($rows, 'value'));
    }

    #[Test]
    public function findValuesInPeriodReturnsOnlyOrdersMatchingTheStatusFilter(): void
    {
        $rows = $this->findValuesInPeriod(new OrderItemCriteria(['payment' => 'paid', 'shipping' => 'open']), 'total_gross');

        self::assertSame([(int) strtotime('-2 days 12:00')], array_column($rows, 'date'));
    }

    #[Test]
    public function findValuesInPeriodReturnsOnlyOrdersOfTheStoragePids(): void
    {
        $rows = $this->findValuesInPeriod(new OrderItemCriteria([], [self::SECOND_ORDER_STORAGE_PID]), 'total_gross');

        self::assertSame([(int) strtotime('today 12:00')], array_column($rows, 'date'));
    }

    #[Test]
    public function findValuesInPeriodReturnsNoOrdersForAnEmptyListOfStoragePids(): void
    {
        self::assertSame([], $this->findValuesInPeriod(new OrderItemCriteria([], []), 'total_gross'));
    }

    #[Test]
    public function findValuesInPeriodThrowsAnExceptionForAnUnknownField(): void
    {
        $this->expectExceptionObject(
            new InvalidArgumentException('"total_gross; DROP" is no field of the table tx_cart_domain_model_order_item.', 1794946139)
        );

        $this->findValuesInPeriod(new OrderItemCriteria(), 'total_gross; DROP');
    }

    #[Test]
    public function findLatestReturnsTheOrdersWithPaymentAndShipping(): void
    {
        $items = $this->createOrderItemQuery()->findLatest(
            new OrderItemCriteria(['payment' => 'paid']),
            'tx_cart_domain_model_order_item.order_date',
            'asc',
            10
        );

        self::assertSame([1, 3], array_column($items, 'uid'));
        self::assertSame(['Payment 1', 'Payment 3'], array_column($items, 'payment_name'));
        self::assertSame(['open', 'shipped'], array_column($items, 'shipping_status'));
    }

    #[Test]
    public function findStoragePidsReturnsTheStorageFoldersOfAllOrders(): void
    {
        $storagePids = $this->createOrderItemQuery()->findStoragePids();
        sort($storagePids);

        self::assertSame([self::ORDER_STORAGE_PID, self::SECOND_ORDER_STORAGE_PID], $storagePids);
    }

    /**
     * @return list<array{date: int, value: mixed}>
     */
    private function findValuesInPeriod(OrderItemCriteria $criteria, ?string $valueField, string $start = '-2 days 0:00'): array
    {
        return iterator_to_array(
            $this->createOrderItemQuery()->findValuesInPeriod(
                $criteria,
                'order_date',
                $valueField,
                (int) strtotime($start),
                (int) strtotime('today 23:59:59')
            ),
            false
        );
    }
}
