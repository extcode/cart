<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class CartCouponPercentage extends AbstractCartCoupon
{
    protected float $discount;

    protected TaxClass $taxClass;

    public function __construct(
        string $title,
        string $code,
        string $couponType,
        float $discount,
        TaxClass $taxClass,
        float $cartMinPrice
    ) {
        $this->title = $title;
        $this->code = $code;
        $this->couponType = $couponType;
        $this->discount = $discount;
        $this->cartMinPrice = $cartMinPrice;
        $this->taxClass = $taxClass;

        // Currently, percentage vouchers are not combinable, because here then the order of discount calculation for
        // different voucher types plays a role and can lead to different prices in the shopping cart.
        $this->isCombinable = false;
    }

    public function getDiscount(): float
    {
        return $this->discount / 100;
    }

    public function getTranslatedDiscount(): float
    {
        $discount = $this->cart->getGross() * $this->getDiscount();

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
        $net = $this->getGross();

        foreach ($this->getTaxes() as $tax) {
            $net -= $tax;
        }

        return $net;
    }

    public function getTaxClass(): TaxClass
    {
        return $this->taxClass;
    }

    public function getTax(): float
    {
        return 0.0;
    }

    public function getTaxes(): array
    {
        $taxes = [];

        foreach ($this->cart->getTaxes() as $taxClassId => $tax) {
            $taxes[$taxClassId] = $tax * $this->getDiscount();
        }

        return $taxes;
    }
}
