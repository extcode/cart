<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\CartCouponFix;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CartCouponFixTest extends UnitTestCase
{
    protected CartCouponFix $coupon;

    protected string $title;

    protected string $code;

    protected string $couponType;

    protected float $discount;

    protected TaxClass $taxClass;

    protected float $cartMinPrice = 0.0;

    public function setUp(): void
    {
        $this->taxClass = new TaxClass(1, '19', 0.19, 'normal');

        $this->title = 'title';
        $this->code = 'code';
        $this->couponType = 'cartdiscount';
        $this->discount = 10.00;

        $this->coupon = new CartCouponFix(
            $this->title,
            $this->code,
            $this->couponType,
            $this->discount,
            $this->taxClass,
            $this->cartMinPrice
        );

        parent::setUp();
    }

    /**
     * @test
     */
    public function constructCouponWithoutTitleThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new CartCouponFix(
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
    public function constructCouponWithoutCodeThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new CartCouponFix(
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
    public function constructCouponWithoutCouponTypeThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new CartCouponFix(
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
    public function constructCouponWithoutDiscountThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new CartCouponFix(
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
    public function constructCouponWithoutTaxClassThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new CartCouponFix(
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
    public function isCombinableInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->coupon->isCombinable()
        );
    }

    /**
     * @test
     */
    public function constructorSetsIsCombinable(): void
    {
        $this->coupon = new CartCouponFix(
            $this->title,
            $this->code,
            $this->couponType,
            $this->discount,
            $this->taxClass,
            $this->cartMinPrice,
            true
        );

        self::assertTrue(
            $this->coupon->isCombinable()
        );
    }

    /**
     * @test
     */
    public function getDiscountInitiallyReturnsDiscountSetDirectlyByConstructor(): void
    {
        self::assertSame(
            10.00,
            $this->coupon->getDiscount()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsGrossSetDirectlyByConstructor(): void
    {
        self::assertSame(
            10.00,
            $this->coupon->getGross()
        );
    }

    /**
     * @test
     */
    public function getGrossReturnsTranslatedDiscount(): void
    {
        $currencyTranslation = 2.0;

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getCurrencyTranslation'])
            ->setConstructorArgs([[$this->taxClass]])
            ->getMock();
        $cart->method('getCurrencyTranslation')->willReturn($currencyTranslation);

        $this->coupon->setCart($cart);

        self::assertSame(
            5.00,
            $this->coupon->getGross()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsNetSetIndirectlyByConstructor(): void
    {
        $net = $this->discount / ($this->taxClass->getCalc() + 1);
        self::assertSame(
            $net,
            $this->coupon->getNet()
        );
    }

    /**
     * @test
     */
    public function getTaxClassInitiallyReturnsTaxClassSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->taxClass,
            $this->coupon->getTaxClass()
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsTaxSetIndirectlyByConstructor(): void
    {
        $tax = $this->discount - ($this->discount / ($this->taxClass->getCalc() + 1));

        self::assertSame(
            $tax,
            $this->coupon->getTax()
        );
    }

    /**
     * @test
     */
    public function getCartMinPriceInitiallyReturnsCartMinPriceSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->cartMinPrice,
            $this->coupon->getCartMinPrice()
        );
    }

    /**
     * @test
     */
    public function isUsableReturnsTrueIfCartMinPriceIsLessToGivenPrice(): void
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 9.99;

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([[$this->taxClass]])
            ->getMock();
        $cart->method('getGross')->willReturn($gross);

        $coupon = new CartCouponFix(
            $this->title,
            $this->code,
            $this->couponType,
            $discount,
            $this->taxClass,
            $cartMinPrice,
            true
        );
        $coupon->setCart($cart);

        self::assertTrue(
            $coupon->isUseable()
        );
    }

    /**
     * @test
     */
    public function isUsableReturnsTrueIfCartMinPriceIsEqualToGivenPrice(): void
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 10.00;

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([[$this->taxClass]])
            ->getMock();
        $cart->method('getGross')->willReturn($gross);

        $coupon = new CartCouponFix(
            $this->title,
            $this->code,
            $this->couponType,
            $discount,
            $this->taxClass,
            $cartMinPrice,
            true
        );
        $coupon->setCart($cart);

        self::assertTrue(
            $coupon->isUseable()
        );
    }

    /**
     * @test
     */
    public function isUsableReturnsFalseIfCartMinPriceIsGreaterToGivenPrice(): void
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 10.01;

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([[$this->taxClass]])
            ->getMock();
        $cart->method('getGross')->willReturn($gross);

        $coupon = new CartCouponFix(
            $this->title,
            $this->code,
            $this->couponType,
            $discount,
            $this->taxClass,
            $cartMinPrice,
            true
        );
        $coupon->setCart($cart);

        self::assertFalse(
            $coupon->isUseable()
        );
    }
}
