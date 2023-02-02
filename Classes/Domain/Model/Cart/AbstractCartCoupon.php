<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

abstract class AbstractCartCoupon implements CartCouponInterface
{
    protected ?Cart $cart = null;

    protected string $title = '';

    protected string $code = '';

    protected string $couponType = '';

    protected float $cartMinPrice = 0.0;

    protected bool $isCombinable = false;

    protected bool $isRelativeDiscount = false;

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getCouponType(): string
    {
        return $this->couponType;
    }

    public function getCartMinPrice(): float
    {
        return $this->cartMinPrice;
    }

    public function isCombinable(): bool
    {
        return $this->isCombinable;
    }

    public function isRelativeDiscount(): bool
    {
        return $this->isRelativeDiscount;
    }

    public function isUseable(): bool
    {
        return $this->cartMinPrice <= $this->cart->getGross();
    }
}
