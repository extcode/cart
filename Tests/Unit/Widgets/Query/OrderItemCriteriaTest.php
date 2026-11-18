<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Widgets\Query;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Widgets\Query\OrderItemCriteria;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(OrderItemCriteria::class)]
class OrderItemCriteriaTest extends UnitTestCase
{
    #[Test]
    public function fromOptionsWithoutOptionsHasNoRestrictions(): void
    {
        $criteria = OrderItemCriteria::fromOptions([]);

        self::assertSame([], $criteria->statusFilter);
        self::assertNull($criteria->storagePids);
    }

    #[Test]
    public function fromOptionsTakesTheStatusOfPaymentAndShipping(): void
    {
        $criteria = OrderItemCriteria::fromOptions([
            'filter' => [
                'payment' => ['status' => 'paid'],
                'shipping' => ['status' => ''],
                'unknown' => ['status' => 'open'],
            ],
        ]);

        self::assertSame(['payment' => ['paid']], $criteria->statusFilter);
    }

    /**
     * @param list<string> $expected
     */
    #[DataProvider('statusListDataProvider')]
    #[Test]
    public function fromOptionsTakesSeveralStatus(mixed $status, array $expected): void
    {
        $criteria = OrderItemCriteria::fromOptions([
            'filter' => [
                'payment' => ['status' => $status],
            ],
        ]);

        self::assertSame(['payment' => $expected], $criteria->statusFilter);
    }

    /**
     * @return iterable<string, array{mixed, list<string>}>
     */
    public static function statusListDataProvider(): iterable
    {
        yield 'list' => [['open', 'paid', 'open', ''], ['open', 'paid']];
        yield 'comma separated list' => ['open, pending,paid', ['open', 'pending', 'paid']];
    }

    #[Test]
    public function fromOptionsIgnoresAnEmptyListOfStatus(): void
    {
        $criteria = OrderItemCriteria::fromOptions([
            'filter' => [
                'payment' => ['status' => []],
                'shipping' => ['status' => ' , '],
            ],
        ]);

        self::assertSame([], $criteria->statusFilter);
    }

    /**
     * @param null|list<int> $expected
     */
    #[DataProvider('storagePidsDataProvider')]
    #[Test]
    public function fromOptionsTakesTheValidStoragePids(mixed $storagePids, ?array $expected): void
    {
        self::assertSame($expected, OrderItemCriteria::fromOptions(['storagePids' => $storagePids])->storagePids);
    }

    /**
     * @return iterable<string, array{mixed, null|list<int>}>
     */
    public static function storagePidsDataProvider(): iterable
    {
        yield 'list' => [[105, '106', 105], [105, 106]];
        yield 'comma separated list' => ['105,106', [105, 106]];
        yield 'single integer' => [105, [105]];
        yield 'invalid values only' => [['abc', 0, -1], null];
        yield 'empty string' => ['', null];
    }

    #[Test]
    public function withStoragePidsKeepsTheStatusFilter(): void
    {
        $criteria = (new OrderItemCriteria(['payment' => ['paid']], [105]))->withStoragePids([]);

        self::assertSame(['payment' => ['paid']], $criteria->statusFilter);
        self::assertSame([], $criteria->storagePids);
    }
}
