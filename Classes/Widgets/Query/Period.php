<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets\Query;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Period up to now in the time zone of the server, e.g. the current month.
 */
final readonly class Period
{
    public const TODAY = 'today';

    public const WEEK = 'week';

    public const MONTH = 'month';

    public const YEAR = 'year';

    private const START_MODIFIERS = [
        self::TODAY => 'today',
        self::WEEK => 'monday this week',
        self::MONTH => 'first day of this month midnight',
        self::YEAR => 'first day of january this year midnight',
    ];

    public function __construct(
        public int $start,
        public int $end,
    ) {
    }

    /**
     * Creates the period from its name (today, week, month or year). The
     * period starts at the beginning of the day, week (Monday), month or year
     * and ends at the end of today.
     *
     * @throws InvalidArgumentException if the name is no known period
     */
    public static function fromName(string $name, ?DateTimeImmutable $now = null): self
    {
        if (!array_key_exists($name, self::START_MODIFIERS)) {
            throw new InvalidArgumentException(
                sprintf(
                    '"%s" is no known period, use one of "%s".',
                    $name,
                    implode('", "', array_keys(self::START_MODIFIERS))
                ),
                1795029457
            );
        }

        $now ??= new DateTimeImmutable();

        return new self(
            $now->modify(self::START_MODIFIERS[$name])->getTimestamp(),
            $now->modify('tomorrow')->getTimestamp() - 1
        );
    }
}
