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
 * Turnover per day, see AbstractPerDayProvider for the options.
 *
 * Additional option:
 * - sum: field of the order item to sum up, defaults to total_gross
 */
class TurnoverPerDayProvider extends AbstractPerDayProvider
{
    public function getOrderItemsInPeriod(int $start, int $end): float
    {
        return (float) array_sum($this->getValuesPerDay($start, $end));
    }

    protected function getDefaultOptions(): array
    {
        return [
            'sum' => 'total_gross',
        ];
    }

    protected function getValueField(): string
    {
        $sum = $this->options['sum'];

        return is_string($sum) && $sum !== '' ? $sum : 'total_gross';
    }

    protected function castValue(mixed $value): float
    {
        return is_numeric($value) ? (float) $value : 0.0;
    }
}
