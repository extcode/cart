<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Widgets\Query;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;
use DateTimeZone;
use Extcode\Cart\Widgets\Query\Period;
use Generator;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Period::class)]
class PeriodTest extends UnitTestCase
{
    #[DataProvider('periodDataProvider')]
    #[Test]
    public function fromNameStartsAtTheBeginningOfThePeriodAndEndsAtTheEndOfToday(string $now, string $name, string $expectedStart): void
    {
        $timeZone = new DateTimeZone('Europe/Berlin');

        $now = new DateTimeImmutable($now, $timeZone);

        $period = Period::fromName($name, $now);

        self::assertSame((new DateTimeImmutable($expectedStart, $timeZone))->getTimestamp(), $period->start);
        self::assertSame($now->setTime(23, 59, 59)->getTimestamp(), $period->end);
    }

    /**
     * @return Generator<string, array{string, string, string}>
     */
    public static function periodDataProvider(): iterable
    {
        yield 'today' => ['2026-11-18 14:30:00', Period::TODAY, '2026-11-18 00:00:00'];
        yield 'week' => ['2026-11-18 14:30:00', Period::WEEK, '2026-11-16 00:00:00'];
        yield 'week on Monday' => ['2026-11-16 14:30:00', Period::WEEK, '2026-11-16 00:00:00'];
        yield 'month' => ['2026-11-18 14:30:00', Period::MONTH, '2026-11-01 00:00:00'];
        yield 'year' => ['2026-11-18 14:30:00', Period::YEAR, '2026-01-01 00:00:00'];
    }

    #[Test]
    public function fromNameStartsTheWeekOnSundayAtTheMondayBefore(): void
    {
        $timeZone = new DateTimeZone('Europe/Berlin');

        $period = Period::fromName(Period::WEEK, new DateTimeImmutable('2026-11-22 14:30:00', $timeZone));

        self::assertSame((new DateTimeImmutable('2026-11-16 00:00:00', $timeZone))->getTimestamp(), $period->start);
    }

    #[Test]
    public function fromNameThrowsAnExceptionForAnUnknownPeriod(): void
    {
        $this->expectExceptionObject(
            new InvalidArgumentException('"decade" is no known period, use one of "today", "week", "month", "year".', 1795029457)
        );

        Period::fromName('decade');
    }
}
