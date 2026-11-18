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
use Extcode\Cart\Widgets\Provider\AbstractPeriodProvider;
use Extcode\Cart\Widgets\Provider\OrdersInPeriodProvider;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(OrdersInPeriodProvider::class)]
#[CoversClass(AbstractPeriodProvider::class)]
class OrdersInPeriodProviderTest extends AbstractWidgetTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createOrder(1, 'today 12:00');
        $this->createOrder(2, 'today 13:00', 'open', 'open');
        $this->createOrder(3, 'first day of this month 12:00');
        $this->createOrder(4, 'last day of previous month 12:00');
    }

    #[Test]
    public function getNumberCountsTheOrdersOfTodayByDefault(): void
    {
        self::assertSame(
            date('j') === '1' ? 3 : 2,
            $this->createProvider()->getNumber()
        );
    }

    #[Test]
    public function getNumberCountsTheOrdersOfTheConfiguredPeriod(): void
    {
        self::assertSame(3, $this->createProvider(['period' => 'month'])->getNumber());
    }

    #[Test]
    public function getNumberCountsOnlyOrdersMatchingTheConfiguredStatusFilter(): void
    {
        self::assertSame(2, $this->createProvider([
            'period' => 'month',
            'filter' => ['payment' => ['status' => 'paid']],
        ])->getNumber());
    }

    #[Test]
    public function getNumberCountsOnlyOrdersInStorageFoldersTheEditorMayShow(): void
    {
        $this->createSecondStorageFolder();
        $this->createOrder(5, 'today 12:00', pid: self::SECOND_ORDER_STORAGE_PID);
        $this->setUpEditor();

        self::assertSame(1, $this->createProvider()->getNumber());
    }

    /**
     * @param array<string, mixed> $options
     */
    private function createProvider(array $options = []): OrdersInPeriodProvider
    {
        return new OrdersInPeriodProvider(
            $this->createOrderItemQuery(),
            $this->createStoragePidAccess(),
            $options
        );
    }
}
