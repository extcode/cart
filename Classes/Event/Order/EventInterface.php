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

interface EventInterface
{
    public function __construct(Cart $cart, OrderItem $orderItem, array $settings = []);

    public function getCart(): Cart;

    public function getOrderItem(): OrderItem;

    public function getSettings(): array;
}
