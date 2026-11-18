<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets\Provider;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Widgets\Access\StoragePidAccess;
use Extcode\Cart\Widgets\Query\OrderItemCriteria;
use Extcode\Cart\Widgets\Query\OrderItemQuery;
use Extcode\Cart\Widgets\Query\Period;

/**
 * Base for the number providers with one value for a period.
 *
 * Options:
 * - filter, storagePids: see OrderItemCriteria::fromOptions()
 * - fieldName: date field of the order item, defaults to order_date
 * - period: today, week, month or year, see Period::fromName(), defaults to today
 */
abstract class AbstractPeriodProvider
{
    /**
     * @var array<string, mixed>
     */
    protected readonly array $options;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        protected readonly OrderItemQuery $orderItemQuery,
        private readonly StoragePidAccess $storagePidAccess,
        array $options = [],
    ) {
        $this->options = array_merge(
            [
                'fieldName' => 'order_date',
                'period' => Period::TODAY,
            ],
            $this->getDefaultOptions(),
            $options
        );
    }

    /**
     * @return array<string, mixed>
     */
    protected function getDefaultOptions(): array
    {
        return [];
    }

    protected function getCriteria(): OrderItemCriteria
    {
        return $this->storagePidAccess->restrict(OrderItemCriteria::fromOptions($this->options));
    }

    protected function getFieldName(): string
    {
        $fieldName = $this->options['fieldName'];

        return is_string($fieldName) && $fieldName !== '' ? $fieldName : 'order_date';
    }

    protected function getPeriod(): Period
    {
        $period = $this->options['period'];

        return Period::fromName(is_string($period) && $period !== '' ? $period : Period::TODAY);
    }
}
