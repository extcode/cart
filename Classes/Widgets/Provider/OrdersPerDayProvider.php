<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets\Provider;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Number of orders per day, see AbstractPerDayProvider for the options.
 */
class OrdersPerDayProvider extends AbstractPerDayProvider
{
    public function getOrderItemsInPeriod(int $start, int $end): int
    {
        return (int) array_sum($this->getValuesPerDay($start, $end));
    }

    protected function getValueField(): ?string
    {
        return null;
    }

    protected function castValue(mixed $value): int
    {
        return is_numeric($value) ? (int) $value : 0;
    }
}
