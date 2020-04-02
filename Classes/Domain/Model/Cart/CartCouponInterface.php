<?php

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

interface CartCouponInterface
{
    /**
     * Returns the coupon title.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Returns the coupon code.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Returns true if a coupon can be combined with other coupons.
     *
     * @return bool
     */
    public function getIsCombinable(): bool;

    /**
     * Returns the Discount
     *
     * @return float
     */
    public function getDiscount(): float;

    /**
     * Returns Gross of Discount
     *
     * @return float
     */
    public function getGross(): float;

    /**
     * Returns Net of Discount
     *
     * @return float
     */
    public function getNet(): float;

    /**
     * Returns Tax Class
     *
     * @return TaxClass
     */
    public function getTaxClass(): TaxClass;

    /**
     * Returns Tax of Discount
     *
     * @return float
     */
    public function getTax(): float;

    /**
     * Returns true if a voucher is applicable.
     *
     * @return bool
     */
    public function getIsUseable(): bool;
}
