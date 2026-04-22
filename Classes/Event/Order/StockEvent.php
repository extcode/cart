<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use Psr\EventDispatcher\StoppableEventInterface;

final class StockEvent implements EventInterface, StoppableEventInterface
{
    private bool $isPropagationStopped = false;

    public function __construct(
        private readonly Cart $cart,
        private readonly OrderItem $orderItem,
        private array $settings = []
    ) {
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getOrderItem(): OrderItem
    {
        return $this->orderItem;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function setSettings(array $settings): void
    {
        $this->settings = $settings;
    }

    public function setPropagationStopped(bool $isPropagationStopped): void
    {
        $this->isPropagationStopped = $isPropagationStopped;
    }

    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }
}
