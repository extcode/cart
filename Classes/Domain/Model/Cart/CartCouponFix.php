<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class CartCouponFix extends AbstractCartCoupon
{
    protected float $discount;

    protected TaxClass $taxClass;

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

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getTranslatedDiscount(): float
    {
        $discount = $this->getDiscount();

        if ($this->cart) {
            $discount = $this->cart->translatePrice($discount);
        }

        return $discount;
    }

    public function getGross(): float
    {
        return $this->getTranslatedDiscount();
    }

    public function getNet(): float
    {
        return $this->getTranslatedDiscount() / ($this->getTaxClass()->getCalc() + 1);
    }

    public function getTaxClass(): TaxClass
    {
        return $this->taxClass;
    }

    public function getTax(): float
    {
        return $this->getTranslatedDiscount() - ($this->getTranslatedDiscount() / ($this->getTaxClass()->getCalc() + 1));
    }
}
