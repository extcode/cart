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
use TYPO3\CMS\Dashboard\Widgets\ListDataProviderInterface;

/**
 * List of the latest orders with name and status of payment and shipping.
 *
 * Options:
 * - filter, storagePids: see OrderItemCriteria::fromOptions()
 * - order_by: field to sort by, defaults to crdate
 * - order_order: sort direction, defaults to desc
 * - limit: number of orders, defaults to 10
 */
class OrderItemsProvider implements ListDataProviderInterface
{
    /**
     * @var array<string, mixed>
     */
    private readonly array $options;

    /**
     * @param array<string, mixed> $options
     */
    public function __construct(
        private readonly OrderItemQuery $orderItemQuery,
        private readonly StoragePidAccess $storagePidAccess,
        array $options = [],
    ) {
        $this->options = array_merge(
            [
                'order_by' => 'crdate',
                'order_order' => 'desc',
                'limit' => 10,
            ],
            $options
        );
    }

    /**
     * @return list<array<string, mixed>>
     */
    public function getItems(): array
    {
        $orderBy = $this->options['order_by'];
        $order = $this->options['order_order'];
        $limit = $this->options['limit'];

        return $this->orderItemQuery->findLatest(
            $this->storagePidAccess->restrict(OrderItemCriteria::fromOptions($this->options)),
            is_string($orderBy) && $orderBy !== '' ? $orderBy : 'crdate',
            is_string($order) ? $order : 'desc',
            is_numeric($limit) ? (int) $limit : 10
        );
    }
}
