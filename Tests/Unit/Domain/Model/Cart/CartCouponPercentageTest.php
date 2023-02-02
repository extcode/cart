<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\CartCouponPercentage;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CartCouponPercentageTest extends UnitTestCase
{
    protected CartCouponPercentage $coupon;

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
        $this->couponType = CartCouponPercentage::class;
        $this->discount = 10.00;

        $this->coupon = new CartCouponPercentage(
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

        $this->coupon = new CartCouponPercentage(
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

        $this->coupon = new CartCouponPercentage(
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

        $this->coupon = new CartCouponPercentage(
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

        $this->coupon = new CartCouponPercentage(
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

        $this->coupon = new CartCouponPercentage(
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
    public function getDiscountInitiallyReturnsDiscountSetDirectlyByConstructor(): void
    {
        self::assertSame(
            0.10,
            $this->coupon->getDiscount()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsGrossSetDirectlyByConstructor(): void
    {
        $taxClass = new TaxClass(1, '19', 0.19, 'normal');

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([[$taxClass]])
            ->getMock();
        $cart->method('getGross')->willReturn(0.0);

        $this->coupon->setCart($cart);

        self::assertSame(
            0.00,
            $this->coupon->getGross()
        );
    }

    /**
     * @test
     */
    public function getGrossReturnsTranslatedDiscount(): void
    {
        $taxClass = new TaxClass(1, '19', 0.19, 'normal');
        $currencyTranslation = 1.0;

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross', 'getCurrencyTranslation'])
            ->setConstructorArgs([[$taxClass]])
            ->getMock();
        $cart->method('getGross')->willReturn(100.0);
        $cart->method('getCurrencyTranslation')->willReturn($currencyTranslation);

        $this->coupon->setCart($cart);

        self::assertSame(
            10.00,
            $this->coupon->getGross()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsNetSetIndirectlyByConstructor(): void
    {
        $taxClass = new TaxClass(1, '19', 0.19, 'normal');

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([[$taxClass]])
            ->getMock();
        $cart->method('getGross')->willReturn(0.0);

        $this->coupon->setCart($cart);

        self::assertSame(
            0.0,
            $this->coupon->getNet()
        );

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross', 'getTaxes'])
            ->setConstructorArgs([[$taxClass]])
            ->getMock();
        $cart->method('getGross')->willReturn(100.0);
        $cart->method('getTaxes')->willReturn([$taxClass->getId() => 19.0]);

        $this->coupon->setCart($cart);

        self::assertSame(
            $this->coupon->getGross() - (19.0 * 0.1),
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
        self::assertSame(
            0.0,
            $this->coupon->getTax()
        );
    }

    /**
     * @test
     */
    public function getTaxesInitiallyReturnsTaxesSetIndirectlyByConstructor(): void
    {
        $taxClass = new TaxClass(1, '19', 0.19, 'normal');

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([[$taxClass]])
            ->getMock();
        $cart->method('getGross')->willReturn(0.0);

        $this->coupon->setCart($cart);

        self::assertSame(
            [],
            $this->coupon->getTaxes()
        );

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross', 'getTaxes'])
            ->setConstructorArgs([[$taxClass]])
            ->getMock();
        $cart->method('getGross')->willReturn(100.0);
        $cart->method('getTaxes')->willReturn([$taxClass->getId() => 19.0]);

        $this->coupon->setCart($cart);

        self::assertSame(
            [$taxClass->getId() => 19.0 * 0.1],
            $this->coupon->getTaxes()
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

        $coupon = new CartCouponPercentage(
            $this->title,
            $this->code,
            $this->couponType,
            $discount,
            $this->taxClass,
            $cartMinPrice
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

        $coupon = new CartCouponPercentage(
            $this->title,
            $this->code,
            $this->couponType,
            $discount,
            $this->taxClass,
            $cartMinPrice
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

        $coupon = new CartCouponPercentage(
            $this->title,
            $this->code,
            $this->couponType,
            $discount,
            $this->taxClass,
            $cartMinPrice
        );
        $coupon->setCart($cart);

        self::assertFalse(
            $coupon->isUseable()
        );
    }
}
