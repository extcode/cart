<?php

declare(strict_types=1);

namespace Extcode\Cart\Event;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;

final class ProcessOrderCheckStockEvent implements ProcessOrderCheckStockEventInterface
{
    public function __construct(
        private readonly Cart $cart
    ) {
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }
}
