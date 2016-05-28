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
 * Cart CartCoupon
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CartCoupon implements \Extcode\Cart\Domain\Model\Cart\CartCouponInterface
{

    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart = null;

    /**
     * Title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Code
     *
     * @var string
     */
    protected $code = '';

    /**
     * Is Combinable
     *
     * @var bool
     */
    protected $isCombinable = false;

    /**
     * Is Relative Discount
     *
     * @var bool
     */
    protected $isRelativeDiscount = false;

    /**
     * Discount
     *
     * @var float
     */
    protected $discount;

    /**
     * Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass;

    /**
     * Cart Min Price
     *
     * @var float
     */
    protected $cartMinPrice = 0.0;

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
        if (!$title) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $title for constructor.',
                1448230010
            );
        }
        if (!$code) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $code for constructor.',
                1448230020
            );
        }
        if (!$discount) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $discount for constructor.',
                1448230030
            );
        }
        if (!$taxClass) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $taxClass for constructor.',
                1448230040
            );
        }

        $this->title = $title;
        $this->code = $code;
        $this->discount = $discount;
        $this->cartMinPrice = $cartMinPrice;
        $this->taxClass = $taxClass;
        $this->isCombinable = $isCombinable;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @return void
     */
    public function setCart($cart)
    {
        $this->cart = $cart;
    }

    /**
     * Gets Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets Code
     *
     * @return string
     */
    public function getCode()
    {
        return $this->code;
    }

    /**
     * Returns is Combinable
     * @return bool
     */
    public function getIsCombinable()
    {
        return $this->isCombinable;
    }

    /**
     * Returns is Relative Discount
     * @return bool
     */
    public function getIsRelativeDiscount()
    {
        return $this->isRelativeDiscount;
    }

    /**
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Returns Gross of Discount
     *
     * @return float
     */
    public function getGross()
    {
        return $this->getDiscount();
    }

    /**
     * Returns Net of Discount
     *
     * @return float
     */
    public function getNet()
    {
        $net = $this->getDiscount() / ($this->getTaxClass()->getCalc() + 1);
        return $net;
    }

    /**
     * Returns Tax Class
     *
     * @return \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * Returns Tax of Discount
     *
     * @return float
     */
    public function getTax()
    {
        $tax = $this->getDiscount() - ($this->getDiscount() / ($this->getTaxClass()->getCalc() + 1));
        return $tax;
    }

    /**
     * Returns Cart Min Price
     *
     * @return float
     */
    public function getCartMinPrice()
    {
        return $this->cartMinPrice;
    }

    /**
     * Return Is Useable For A Given Price
     *
     * @return bool
     */
    public function getIsUseable()
    {
        $isUseable = $this->cartMinPrice <= $this->cart->getGross();
        return $isUseable;
    }
}
