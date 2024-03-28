<?php

namespace Extcode\Cart\Event;

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;
use TYPO3\CMS\Extbase\Mvc\Request;

final class ShowCartEvent implements ShowCartEventInterface
{
    public function __construct(
        private Cart $cart,
        private readonly Request $request,
        private readonly array $settings,
        private Item $orderItem,
        private BillingAddress $billingAddress,
        private ShippingAddress $shippingAddress
    ) {}

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }

    public function getOrderItem(): Item
    {
        return $this->orderItem;
    }
    public function setOrderItem(Item $orderItem): void
    {
        $this->orderItem = $orderItem;
    }
    public function getBillingAddress(): BillingAddress
    {
        return $this->billingAddress;
    }
    public function setBillingAddress(BillingAddress $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }
    public function getShippingAddress(): ShippingAddress
    {
        return $this->shippingAddress;
    }
    public function setShippingAddress(ShippingAddress $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }
}
