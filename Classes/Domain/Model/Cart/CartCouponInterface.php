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
     */
    public function getIsCombinable(): bool;

    /**
     * Returns the Discount
     *
     * @deprecated will be removed in v9.x for TYPO3 v12 and v11. For internal use only.
     */
    public function getDiscount(): float;

    /**
     * Returns calculated Gross of Discount
     */
    public function getGross(): float;

    /**
     * Returns calculated Net of Discount
     */
    public function getNet(): float;

    /**
     * Returns Tax Class
     *
     * @deprecated will be removed in v9.x for TYPO3 v12 and v11.
     */
    public function getTaxClass(): TaxClass;

    /**
     * Returns Tax of Discount
     *
     * @deprecated will be replaced by a getTaxes() method in v9.x for TYPO3 v12 and v11.
     */
    public function getTax(): float;

    /**
     * Returns true if a voucher is applicable.
     */
    public function getIsUseable(): bool;
}
