<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;

class CartCouponTest extends UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Cart\CartCoupon
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
     * Coupon Type
     *
     * @var string
     */
    protected $couponType;

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

        $this->title = 'title';
        $this->code = 'code';
        $this->couponType = 'cartdiscount';
        $this->discount = 10.00;

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $this->couponType,
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
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $title for constructor.',
            1448230010
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            null,
            $this->code,
            $this->couponType,
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
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $code for constructor.',
            1448230020
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            null,
            $this->couponType,
            $this->discount,
            $this->taxClass,
            $this->cartMinPrice
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutCouponTypeThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $couponType for constructor.',
            1468928203
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
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
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $discount for constructor.',
            1448230030
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $this->couponType,
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
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $taxClass for constructor.',
            1448230040
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $this->couponType,
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
        $this->coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $this->couponType,
            $this->discount,
            $this->taxClass,
            $this->cartMinPrice,
            true
        );

        $this->assertTrue(
            $this->coupon->getIsCombinable()
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
    public function getGrossReturnsTranslatedDiscount()
    {
        $currencyTranslation = 2.0;

        $cart = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getCurrencyTranslation'])
            ->setConstructorArgs([[$this->taxClass]])
            ->getMock();
        $cart->expects($this->any())->method('getCurrencyTranslation')->will($this->returnValue($currencyTranslation));

        $this->coupon->setCart($cart);

        $this->assertSame(
            5.00,
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

        $cart = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getGross'])
            ->setConstructorArgs([[$this->taxClass]])
            ->getMock();
        $cart->expects($this->any())->method('getGross')->will($this->returnValue($gross));

        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $this->couponType,
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

        $cart = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getGross'])
            ->setConstructorArgs([[$this->taxClass]])
            ->getMock();
        $cart->expects($this->any())->method('getGross')->will($this->returnValue($gross));

        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $this->couponType,
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

        $cart = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getGross'])
            ->setConstructorArgs([[$this->taxClass]])
            ->getMock();
        $cart->expects($this->any())->method('getGross')->will($this->returnValue($gross));

        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            $this->title,
            $this->code,
            $this->couponType,
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
