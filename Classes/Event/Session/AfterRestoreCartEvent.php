<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Session;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Psr\EventDispatcher\StoppableEventInterface;

final class AfterRestoreCartEvent implements StoppableEventInterface
{
    private bool $isPropagationStopped = false;

    public function __construct(private Cart $cart) {}

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
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
