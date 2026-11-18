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
use Extcode\Cart\Widgets\Provider\TurnoverInPeriodProvider;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(TurnoverInPeriodProvider::class)]
class TurnoverInPeriodProviderTest extends AbstractWidgetTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->createOrder(1, 'today 12:00', totalGross: 10.5);
        $this->createOrder(2, 'today 13:00', 'canceled', 'open', 20.25);
        $this->createOrder(3, 'first day of this month 12:00', totalGross: 5.0);
        $this->createOrder(4, 'last day of previous month 12:00', totalGross: 100.0);
    }

    #[Test]
    public function getAmountSumsTheGrossTotalOfTheOrdersOfTodayByDefault(): void
    {
        self::assertSame(
            date('j') === '1' ? 35.75 : 30.75,
            $this->createProvider()->getAmount()
        );
    }

    #[Test]
    public function getAmountSumsTheGrossTotalOfTheConfiguredPeriod(): void
    {
        self::assertSame(35.75, $this->createProvider(['period' => 'month'])->getAmount());
    }

    #[Test]
    public function getAmountSumsOnlyOrdersMatchingTheConfiguredStatusFilter(): void
    {
        self::assertSame(15.5, $this->createProvider([
            'period' => 'month',
            'filter' => ['payment' => ['status' => 'paid']],
        ])->getAmount());
    }

    #[Test]
    public function getAmountSumsTheConfiguredField(): void
    {
        $this->getConnectionPool()->getConnectionForTable('tx_cart_domain_model_order_item')->update(
            'tx_cart_domain_model_order_item',
            ['total_net' => 8.4],
            ['uid' => 1]
        );

        self::assertSame(8.4, $this->createProvider(['sum' => 'total_net', 'period' => 'month'])->getAmount());
    }

    #[Test]
    public function getAmountThrowsAnExceptionForAnUnknownPeriod(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionCode(1795029457);

        $this->createProvider(['period' => 'decade'])->getAmount();
    }

    /**
     * @param array<string, mixed> $options
     */
    private function createProvider(array $options = []): TurnoverInPeriodProvider
    {
        return new TurnoverInPeriodProvider(
            $this->createOrderItemQuery(),
            $this->createStoragePidAccess(),
            $options
        );
    }
}
