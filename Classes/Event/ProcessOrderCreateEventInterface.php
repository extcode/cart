<?php

namespace Extcode\Cart\Event;

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item as OrderItem;

interface ProcessOrderCreateEventInterface
{
    public function __construct(Cart $cart, OrderItem $orderItem, array $settings = []);

    public function getCart(): Cart;

    public function getOrderItem(): OrderItem;

    public function getSettings(): array;
}
