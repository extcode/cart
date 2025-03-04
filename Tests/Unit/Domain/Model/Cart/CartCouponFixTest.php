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
use Extcode\Cart\Service\CurrencyTranslationService;
use Extcode\Cart\Service\CurrencyTranslationServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(CartCouponFix::class)]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function isCombinableInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->coupon->isCombinable()
        );
    }

    #[Test]
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

    #[Test]
    public function getDiscountInitiallyReturnsDiscountSetDirectlyByConstructor(): void
    {
        self::assertSame(
            10.00,
            $this->coupon->getDiscount()
        );
    }

    #[Test]
    public function getGrossInitiallyReturnsGrossSetDirectlyByConstructor(): void
    {
        self::assertSame(
            10.00,
            $this->coupon->getGross()
        );
    }

    #[Test]
    public function getGrossReturnsTranslatedDiscount(): void
    {
        $currencyTranslation = 2.0;

        $cart = $this->createCartMock(['getGross', 'getCurrencyTranslation']);
        $cart->method('getCurrencyTranslation')->willReturn($currencyTranslation);

        $this->coupon->setCart($cart);

        self::assertSame(
            5.00,
            $this->coupon->getGross()
        );
    }

    #[Test]
    public function getNetInitiallyReturnsNetSetIndirectlyByConstructor(): void
    {
        $net = $this->discount / ($this->taxClass->getCalc() + 1);
        self::assertSame(
            $net,
            $this->coupon->getNet()
        );
    }

    #[Test]
    public function getTaxClassInitiallyReturnsTaxClassSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->taxClass,
            $this->coupon->getTaxClass()
        );
    }

    #[Test]
    public function getTaxInitiallyReturnsTaxSetIndirectlyByConstructor(): void
    {
        $tax = $this->discount - ($this->discount / ($this->taxClass->getCalc() + 1));

        self::assertSame(
            $tax,
            $this->coupon->getTax()
        );
    }

    #[Test]
    public function getCartMinPriceInitiallyReturnsCartMinPriceSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->cartMinPrice,
            $this->coupon->getCartMinPrice()
        );
    }

    #[Test]
    public function isUsableReturnsTrueIfCartMinPriceIsLessToGivenPrice(): void
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 9.99;

        $cart = $this->createCartMock();
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

    #[Test]
    public function isUsableReturnsTrueIfCartMinPriceIsEqualToGivenPrice(): void
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 10.00;

        $cart = $this->createCartMock();
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

    #[Test]
    public function isUsableReturnsFalseIfCartMinPriceIsGreaterToGivenPrice(): void
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 10.01;

        $cart = $this->createCartMock();
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

    private function createCartMock(array $methods = ['getGross']): Cart|MockObject
    {
        GeneralUtility::addInstance(
            CurrencyTranslationServiceInterface::class,
            new CurrencyTranslationService()
        );

        return $this->getMockBuilder(Cart::class)
            ->onlyMethods($methods)
            ->setConstructorArgs([[$this->taxClass]])
            ->getMock();
    }
}
