<?php

declare(strict_types=1);

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
use Extcode\Cart\Service\CurrencyTranslationService;
use Extcode\Cart\Service\CurrencyTranslationServiceInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(CartCouponPercentage::class)]
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
        $this->taxClass = new TaxClass(1, '19 %', 0.19, 'normal');

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

    #[Test]
    public function isCombinableInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->coupon->isCombinable()
        );
    }

    #[Test]
    public function getDiscountInitiallyReturnsDiscountSetDirectlyByConstructor(): void
    {
        self::assertSame(
            0.10,
            $this->coupon->getDiscount()
        );
    }

    #[Test]
    public function getGrossInitiallyReturnsGrossSetDirectlyByConstructor(): void
    {
        $taxClass = new TaxClass(1, '19 %', 0.19, 'normal');

        $cart = $this->createCartMock();
        $cart->method('getGross')->willReturn(0.0);

        $this->coupon->setCart($cart);

        self::assertSame(
            0.00,
            $this->coupon->getGross()
        );
    }

    #[Test]
    public function getGrossReturnsTranslatedDiscount(): void
    {
        $taxClass = new TaxClass(1, '19 %', 0.19, 'normal');
        $currencyTranslation = 1.0;

        $cart = $this->createCartMock(['getGross', 'getCurrencyTranslation']);
        $cart->method('getGross')->willReturn(100.0);
        $cart->method('getCurrencyTranslation')->willReturn($currencyTranslation);

        $this->coupon->setCart($cart);

        self::assertSame(
            10.00,
            $this->coupon->getGross()
        );
    }

    #[Test]
    public function getNetInitiallyReturnsNetSetIndirectlyByConstructor(): void
    {
        $taxClass = new TaxClass(1, '19 %', 0.19, 'normal');

        $cart = $this->createCartMock();
        $cart->method('getGross')->willReturn(0.0);

        $this->coupon->setCart($cart);

        self::assertSame(
            0.0,
            $this->coupon->getNet()
        );

        $cart = $this->createCartMock(['getGross', 'getTaxes']);
        $cart->method('getGross')->willReturn(100.0);
        $cart->method('getTaxes')->willReturn([$taxClass->getId() => 19.0]);

        $this->coupon->setCart($cart);

        self::assertSame(
            $this->coupon->getGross() - (19.0 * 0.1),
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
        self::assertSame(
            0.0,
            $this->coupon->getTax()
        );
    }

    #[Test]
    public function getTaxesInitiallyReturnsTaxesSetIndirectlyByConstructor(): void
    {
        $taxClass = new TaxClass(1, '19 %', 0.19, 'normal');

        $cart = $this->createCartMock();
        $cart->method('getGross')->willReturn(0.0);

        $this->coupon->setCart($cart);

        self::assertSame(
            [],
            $this->coupon->getTaxes()
        );

        $cart = $this->createCartMock(['getGross', 'getTaxes']);
        $cart->method('getGross')->willReturn(100.0);
        $cart->method('getTaxes')->willReturn([$taxClass->getId() => 19.0]);

        $this->coupon->setCart($cart);

        self::assertSame(
            [$taxClass->getId() => 19.0 * 0.1],
            $this->coupon->getTaxes()
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

    #[Test]
    public function isUsableReturnsTrueIfCartMinPriceIsEqualToGivenPrice(): void
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 10.00;

        $cart = $this->createCartMock();
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

    #[Test]
    public function isUsableReturnsFalseIfCartMinPriceIsGreaterToGivenPrice(): void
    {
        $gross = 10.00;
        $discount = 5.00;
        $cartMinPrice = 10.01;

        $cart = $this->createCartMock();
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
