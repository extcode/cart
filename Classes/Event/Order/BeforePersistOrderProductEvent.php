<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\BeVariantInterface;
use Extcode\Cart\Domain\Model\Cart\ProductInterface;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Model\Order\Product;

/**
 * Dispatched while the order is persisted, once per ordered product (or
 * backend variant) before it is stored.
 *
 * Listeners of product extensions decide whether the ordered product can be
 * shipped, canceled and returned. The values are a snapshot at order time,
 * they are not changed afterwards when the product changes.
 *
 * The order product and the cart product are passed for information only,
 * change the properties with the setters of this event.
 */
final class BeforePersistOrderProductEvent
{
    public function __construct(
        private readonly Item $orderItem,
        private readonly Product $orderProduct,
        private readonly ProductInterface $cartProduct,
        private readonly ?BeVariantInterface $beVariant,
        private bool $shippable,
        private bool $cancelable,
        private bool $returnable,
    ) {
    }

    public function getOrderItem(): Item
    {
        return $this->orderItem;
    }

    public function getOrderProduct(): Product
    {
        return $this->orderProduct;
    }

    /**
     * The product of the cart, the product of the backend variant if the
     * ordered product is a backend variant.
     */
    public function getCartProduct(): ProductInterface
    {
        return $this->cartProduct;
    }

    /**
     * The backend variant of the ordered product, null for products without
     * backend variants.
     */
    public function getBeVariant(): ?BeVariantInterface
    {
        return $this->beVariant;
    }

    public function isShippable(): bool
    {
        return $this->shippable;
    }

    /**
     * Defaults to true unless the cart product is a virtual product.
     */
    public function setShippable(bool $shippable): void
    {
        $this->shippable = $shippable;
    }

    public function isCancelable(): bool
    {
        return $this->cancelable;
    }

    /**
     * Whether the ordered product may be canceled before it is shipped or
     * before the service is provided, defaults to true.
     *
     * Rules depending on the time of the cancellation (e.g. until 14 days
     * before an event) do not belong here.
     */
    public function setCancelable(bool $cancelable): void
    {
        $this->cancelable = $cancelable;
    }

    public function isReturnable(): bool
    {
        return $this->returnable;
    }

    /**
     * Whether the ordered product may be returned after it was shipped,
     * defaults to true.
     */
    public function setReturnable(bool $returnable): void
    {
        $this->returnable = $returnable;
    }
}
