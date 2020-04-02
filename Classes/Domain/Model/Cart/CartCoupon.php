<?php

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class CartCoupon implements \Extcode\Cart\Domain\Model\Cart\CartCouponInterface
{
    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart = null;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $code = '';

    /**
     * @var string
     */
    protected $couponType = '';

    /**
     * @var bool
     */
    protected $isCombinable = false;

    /**
     * @var bool
     */
    protected $isRelativeDiscount = false;

    /**
     * @var float
     */
    protected $discount;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass;

    /**
     * @var float
     */
    protected $cartMinPrice = 0.0;

    /**
     * @param string $title
     * @param string $code
     * @param string $couponType
     * @param float $discount
     * @param TaxClass $taxClass
     * @param float $cartMinPrice
     * @param bool $isCombinable
     */
    public function __construct(
        string $title,
        string $code,
        string $couponType,
        float $discount,
        TaxClass $taxClass,
        float $cartMinPrice,
        bool $isCombinable = false
    ) {
        $this->title = $title;
        $this->code = $code;
        $this->couponType = $couponType;
        $this->discount = $discount;
        $this->cartMinPrice = $cartMinPrice;
        $this->taxClass = $taxClass;
        $this->isCombinable = $isCombinable;
    }

    /**
     * @param Cart $cart
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getCouponType(): string
    {
        return $this->couponType;
    }

    /**
     * @return bool
     */
    public function getIsCombinable(): bool
    {
        return $this->isCombinable;
    }

    /**
     * @return bool
     */
    public function getIsRelativeDiscount(): bool
    {
        return $this->isRelativeDiscount;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @return float
     */
    public function getTranslatedDiscount(): float
    {
        $discount = $this->getDiscount();

        if ($this->cart) {
            $discount = $this->cart->translatePrice($discount);
        }

        return $discount;
    }

    /**
     * @return float
     */
    public function getGross(): float
    {
        return $this->getTranslatedDiscount();
    }

    /**
     * @return float
     */
    public function getNet(): float
    {
        $net = $this->getTranslatedDiscount() / ($this->getTaxClass()->getCalc() + 1);
        return $net;
    }

    /**
     * @return TaxClass
     */
    public function getTaxClass(): TaxClass
    {
        return $this->taxClass;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        $tax = $this->getTranslatedDiscount() - ($this->getTranslatedDiscount() / ($this->getTaxClass()->getCalc() + 1));
        return $tax;
    }

    /**
     * @return float
     */
    public function getCartMinPrice(): float
    {
        return $this->cartMinPrice;
    }

    /**
     * @return bool
     */
    public function getIsUseable(): bool
    {
        $isUseable = $this->cartMinPrice <= $this->cart->getGross();
        return $isUseable;
    }
}
