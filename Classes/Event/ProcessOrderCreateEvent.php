<?php

namespace Extcode\Cart\Event;

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item as OrderItem;

final class ProcessOrderCreateEvent implements ProcessOrderCreateEventInterface
{
    /**
     * @var Cart
     */
    private $cart;

    /**
     * @var OrderItem
     */
    private $orderItem;

    /**
     * @var array
     */
    private $settings;

    public function __construct(Cart $cart, OrderItem $orderItem, array $settings = [])
    {
        $this->cart = $cart;
        $this->orderItem = $orderItem;
        $this->settings = $settings;
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
}
