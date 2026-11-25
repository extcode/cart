<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use ArrayIterator;
use Countable;
use IteratorAggregate;

/**
 * @implements IteratorAggregate<int, OrderProductQuantity>
 */
final readonly class OrderProductQuantities implements Countable, IteratorAggregate
{
    /**
     * @var array<int, OrderProductQuantity>
     */
    private array $quantitiesByOrderProductUid;

    public function __construct(OrderProductQuantity ...$orderProductQuantities)
    {
        $quantitiesByOrderProductUid = [];
        foreach ($orderProductQuantities as $orderProductQuantity) {
            $quantitiesByOrderProductUid[$orderProductQuantity->orderProductUid] = $orderProductQuantity;
        }
        $this->quantitiesByOrderProductUid = $quantitiesByOrderProductUid;
    }

    public function findByOrderProductUid(int $orderProductUid): ?OrderProductQuantity
    {
        return $this->quantitiesByOrderProductUid[$orderProductUid] ?? null;
    }

    public function hasShippableProducts(): bool
    {
        return $this->getShippableQuantities() !== [];
    }

    public function isAnythingShipped(): bool
    {
        foreach ($this->getShippableQuantities() as $orderProductQuantity) {
            if ($orderProductQuantity->shippedCount > 0) {
                return true;
            }
        }

        return false;
    }

    public function isEverythingShipped(): bool
    {
        foreach ($this->getShippableQuantities() as $orderProductQuantity) {
            if (!$orderProductQuantity->isCompletelyShipped()) {
                return false;
            }
        }

        return $this->hasShippableProducts();
    }

    /**
     * @return ArrayIterator<int, OrderProductQuantity>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->quantitiesByOrderProductUid);
    }

    public function count(): int
    {
        return count($this->quantitiesByOrderProductUid);
    }

    /**
     * @return list<OrderProductQuantity>
     */
    private function getShippableQuantities(): array
    {
        return array_values(array_filter(
            $this->quantitiesByOrderProductUid,
            static fn (OrderProductQuantity $orderProductQuantity): bool => $orderProductQuantity->isShippable
                && $orderProductQuantity->orderedCount > 0
        ));
    }
}
