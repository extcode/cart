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
     * Gets Title
     *
     * @return string
     */
    public function getTitle();

    /**
     * Gets Code
     *
     * @return string
     */
    public function getCode();

    /**
     * Returns is Combinable
     *
     * @return bool
     */
    public function getIsCombinable();

    /**
     * Returns the Discount
     *
     * @return float
     */
    public function getDiscount();

    /**
     * Returns Gross of Discount
     *
     * @return float
     */
    public function getGross();

    /**
     * Returns Net of Discount
     *
     * @return float
     */
    public function getNet();

    /**
     * Returns Tax Class
     *
     * @return \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    public function getTaxClass();

    /**
     * Returns Tax of Discount
     *
     * @return float
     */
    public function getTax();

    /**
     * Return Is Useable For A Given Price
     *
     * @return bool
     */
    public function getIsUseable();
}
