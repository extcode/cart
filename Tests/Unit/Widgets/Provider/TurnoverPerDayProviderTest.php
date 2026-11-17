<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Widgets\Provider;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Widgets\Access\StoragePidAccess;
use Extcode\Cart\Widgets\Provider\AbstractPerDayProvider;
use Extcode\Cart\Widgets\Provider\TurnoverPerDayProvider;
use Extcode\Cart\Widgets\Query\OrderItemCriteria;
use Extcode\Cart\Widgets\Query\OrderItemQuery;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(TurnoverPerDayProvider::class)]
#[CoversClass(AbstractPerDayProvider::class)]
class TurnoverPerDayProviderTest extends UnitTestCase
{
    #[Test]
    public function getChartDataSumsTheValuesPerDayAndFillsDaysWithoutOrders(): void
    {
        $orderItemQuery = $this->createMock(OrderItemQuery::class);
        $orderItemQuery
            ->expects(self::once())
            ->method('findValuesInPeriod')
            ->with(
                self::isInstanceOf(OrderItemCriteria::class),
                'order_date',
                'total_net',
                (int) strtotime('-2 day 0:00:00'),
                (int) strtotime('today 23:59:59')
            )
            ->willReturn([
                ['date' => (int) strtotime('-2 days 10:00'), 'value' => '1.50'],
                ['date' => (int) strtotime('today 9:00'), 'value' => '2.25'],
                ['date' => (int) strtotime('today 18:00'), 'value' => 3],
            ])
        ;

        $provider = new TurnoverPerDayProvider($orderItemQuery, self::createStoragePidAccess(), ['days' => 2, 'sum' => 'total_net']);

        $chartData = $provider->getChartData();

        self::assertSame(
            [date('Y-m-d', (int) strtotime('-2 days')), date('Y-m-d', (int) strtotime('-1 day')), date('Y-m-d')],
            $chartData['labels']
        );
        self::assertIsArray($chartData['datasets']);
        self::assertIsArray($chartData['datasets'][0]);
        self::assertSame([1.5, 0.0, 5.25], $chartData['datasets'][0]['data']);
    }

    #[Test]
    public function getOrderItemsInPeriodSumsAllValues(): void
    {
        $orderItemQuery = self::createStub(OrderItemQuery::class);
        $orderItemQuery->method('findValuesInPeriod')->willReturn([
            ['date' => (int) strtotime('-1 day 10:00'), 'value' => '1.50'],
            ['date' => (int) strtotime('today 9:00'), 'value' => '2.25'],
        ]);

        $provider = new TurnoverPerDayProvider($orderItemQuery, self::createStoragePidAccess());

        self::assertSame(3.75, $provider->getOrderItemsInPeriod(0, (int) strtotime('today 23:59:59')));
    }

    private static function createStoragePidAccess(): StoragePidAccess
    {
        $storagePidAccess = self::createStub(StoragePidAccess::class);
        $storagePidAccess->method('restrict')->willReturnArgument(0);

        return $storagePidAccess;
    }
}
