<?php

namespace Extcode\Cart\Tests\Domain\Model\Cart;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lorenz <ext.cart@extco.de>, extco.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * CartCoupon Test
 *
 * @package cart
 * @author Daniel Lorenz
 * @license http://www.gnu.org/licenses/lgpl.html
 *                     GNU Lesser General Public License, version 3 or later
 */
class CartCouponTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $coupon = null;

    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * Code
     *
     * @var string
     */
    protected $code;

    /**
     * Discount
     *
     * @var float
     */
    protected $discount;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass = null;

    /**
     * Cart Min Price
     *
     * @var float
     */
    protected $cartMinPrice = 0.0;

    /**
     *
     */
    public function setUp()
    {
        $this->taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $this->title = 'CouponTitle';
        $this->code = 'CouponTitle';
        $this->discount = 10.00;

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $this->discount,
            $this->taxClass,
            $this->cartMinPrice
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutTitleThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $title for constructor.',
            1448230010
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            null,
            $this->code,
            $this->discount,
            $this->taxClass,
            $this->cartMinPrice
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutCodeThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $code for constructor.',
            1448230020
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            null,
            $this->discount,
            $this->taxClass,
            $this->cartMinPrice
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutDiscountThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $discount for constructor.',
            1448230030
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            null,
            $this->taxClass,
            $this->cartMinPrice
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutTaxClassThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $taxClass for constructor.',
            1448230040
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $this->discount,
            null,
            $this->cartMinPrice
        );
    }

    /**
     * @test
     */
    public function getIsCombinableInitiallyReturnsFalse()
    {
        $this->assertFalse(
            $this->coupon->getIsCombinable()
        );
    }

    /**
     * @test
     */
    public function constructorSetsIsCombinable()
    {
        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $this->discount,
            $this->taxClass,
            $this->cartMinPrice,
            true
        );

        $this->assertTrue(
            $coupon->getIsCombinable()
        );
    }

    /**
     * @test
     */
    public function getDiscountInitiallyReturnsDiscountSetDirectlyByConstructor()
    {
        $this->assertSame(
            10.00,
            $this->coupon->getDiscount()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsGrossSetDirectlyByConstructor()
    {
        $this->assertSame(
            10.00,
            $this->coupon->getGross()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsNetSetIndirectlyByConstructor()
    {
        $net = $this->discount / ($this->taxClass->getCalc() + 1);
        $this->assertSame(
            $net,
            $this->coupon->getNet()
        );
    }

    /**
     * @test
     */
    public function getTaxClassInitiallyReturnsTaxClassSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->taxClass,
            $this->coupon->getTaxClass()
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsTaxSetIndirectlyByConstructor()
    {
        $tax = $this->discount - ($this->discount / ($this->taxClass->getCalc() + 1));

        $this->assertSame(
            $tax,
            $this->coupon->getTax()
        );
    }

    /**
     * @test
     */
    public function getCartMinPriceInitiallyReturnsCartMinPriceSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->cartMinPrice,
            $this->coupon->getCartMinPrice()
        );
    }

    /**
     * @test
     */
    public function isUsableReturnsTrueIfCartMinPriceIsLessToGivenPrice()
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 9.99;

        $cart = $this->getMock(
            \Extcode\Cart\Domain\Model\Cart\Cart::class,
            array('getGross'),
            array(),
            '',
            false
        );
        $cart->expects($this->any())->method('getGross')->will($this->returnValue($gross));

        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $discount,
            $this->taxClass,
            $cartMinPrice,
            true
        );
        $coupon->setCart($cart);

        $this->assertTrue(
            $coupon->getIsUseable()
        );
    }

    /**
     * @test
     */
    public function isUsableReturnsTrueIfCartMinPriceIsEqualToGivenPrice()
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 10.00;

        $cart = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Cart\\Cart',
            array('getGross'),
            array(),
            '',
            false
        );
        $cart->expects($this->any())->method('getGross')->will($this->returnValue($gross));

        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $discount,
            $this->taxClass,
            $cartMinPrice,
            true
        );
        $coupon->setCart($cart);

        $this->assertTrue(
            $coupon->getIsUseable()
        );
    }

    /**
     * @test
     */
    public function isUsableReturnsFalseIfCartMinPriceIsGreaterToGivenPrice()
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 10.01;

        $cart = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Cart\\Cart',
            array('getGross'),
            array(),
            '',
            false
        );
        $cart->expects($this->any())->method('getGross')->will($this->returnValue($gross));

        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $discount,
            $this->taxClass,
            $cartMinPrice,
            true
        );
        $coupon->setCart($cart);

        $this->assertFalse(
            $coupon->getIsUseable()
        );
    }
}