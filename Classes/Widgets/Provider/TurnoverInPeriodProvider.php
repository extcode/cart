<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets\Provider;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Widgets\AmountDataProviderInterface;

/**
 * Turnover in a period, see AbstractPeriodProvider for the options.
 *
 * Additional option:
 * - sum: field of the order item to sum up, defaults to total_gross
 */
class TurnoverInPeriodProvider extends AbstractPeriodProvider implements AmountDataProviderInterface
{
    public function getAmount(): float
    {
        $period = $this->getPeriod();

        return $this->orderItemQuery->sumInPeriod(
            $this->getCriteria(),
            $this->getFieldName(),
            $this->getValueField(),
            $period->start,
            $period->end
        );
    }

    protected function getDefaultOptions(): array
    {
        return [
            'sum' => 'total_gross',
        ];
    }

    private function getValueField(): string
    {
        $sum = $this->options['sum'];

        return is_string($sum) && $sum !== '' ? $sum : 'total_gross';
    }
}
