<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item;
use Psr\EventDispatcher\StoppableEventInterface;

final class BeforeShowCartEvent implements StoppableEventInterface
{
    private bool $isPropagationStopped = false;

    public function __construct(private Cart $cart, private ?Item $orderItem = null) {}

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
    }

    public function getOrderItem(): ?Item
    {
        return $this->orderItem;
    }

    public function setOrderItem(Item $orderItem): void
    {
        $this->orderItem = $orderItem;
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
