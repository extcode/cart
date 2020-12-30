<?php

namespace Extcode\Cart\Event;

use Extcode\Cart\Domain\Model\Cart\Cart;

final class ProcessOrderCheckStockEvent
{
    /**
     * @var Cart
     */
    private $cart;

    public function __construct(Cart $cart)
    {
        $this->cart = $cart;
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }
}
