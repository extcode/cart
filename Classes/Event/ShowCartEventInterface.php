<?php

namespace Extcode\Cart\Event;

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;
use TYPO3\CMS\Extbase\Mvc\Request;

interface ShowCartEventInterface
{
    public function __construct(
        Cart $cart,
        Request $request,
        array $settings,
        Item $orderItem,
        BillingAddress $billingAddress,
        ShippingAddress $shippingAddress
    );

    public function getCart(): Cart;

    public function getRequest(): Request;

    public function getSettings(): array;

    public function getOrderItem(): Item;

    public function getBillingAddress(): BillingAddress;

    public function getShippingAddress(): ShippingAddress;
}
