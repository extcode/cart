<?php

namespace Extcode\Cart\Domain\Model\Cart;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Cart CartCoupon Model
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CartCoupon extends \Extcode\Cart\Domain\Model\Cart\AbstractCoupon
{
    /**
     * __construct
     *
     * @param string $title
     * @param string $code
     * @param float $discount
     * @param \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     * @param float $cartMinPrice
     * @param bool $isCombinable
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(
        $title,
        $code,
        $discount,
        $taxClass,
        $cartMinPrice,
        $isCombinable = false
    ) {
        parent::__construct($title, $code, $discount, $taxClass, $cartMinPrice, $isCombinable, false);
    }
}
