<?php

declare(strict_types=1);

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
     */
    public function getTitle(): string;

    /**
     * Returns the coupon code.
     */
    public function getCode(): string;

    /**
     * Returns true if a coupon can be combined with other coupons.
     */
    public function isCombinable(): bool;

    /**
     * Returns true if a voucher is applicable.
     */
    public function isUseable(): bool;

    /**
     * Returns calculated Gross of Discount
     */
    public function getGross(): float;

    /**
     * Returns calculated Net of Discount
     */
    public function getNet(): float;

    /**
     * Returns calculated Tax of Discount
     */
    public function getTax(): float;
}
