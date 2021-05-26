<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Coupon;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CouponTest extends UnitTestCase
{
    /**
     * @var Coupon
     */
    protected $coupon;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $code = '';

    /**
     * @var string
     */
    protected $couponType = '';

    /**
     * @var float
     */
    protected $discount = 0.0;

    /**
     * @var int
     */
    protected $taxClassId = 0;

    public function setUp(): void
    {
        $this->title = 'Coupon';
        $this->code = 'coupon';
        $this->couponType = 'cartdiscount';
        $this->discount = 10.00;
        $this->taxClassId = 1;

        $this->coupon = new Coupon(
            $this->title,
            $this->code,
            $this->couponType,
            $this->discount,
            $this->taxClassId
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutTitleThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new Coupon(
            null,
            $this->code,
            $this->couponType,
            $this->discount,
            $this->taxClassId
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutCodeThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new Coupon(
            $this->title,
            null,
            $this->couponType,
            $this->discount,
            $this->taxClassId
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutCouponTypeThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new Coupon(
            $this->title,
            $this->code,
            null,
            $this->discount,
            $this->taxClassId
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutDiscountThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new Coupon(
            $this->title,
            $this->code,
            $this->couponType,
            null,
            $this->taxClassId
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutTaxClassIdThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new Coupon(
            $this->title,
            $this->code,
            $this->couponType,
            $this->discount,
            null
        );
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->coupon->getTitle()
        );
    }

    /**
     * @test
     */
    public function getCodeInitiallyReturnsCodeSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->code,
            $this->coupon->getCode()
        );
    }

    /**
     * @test
     */
    public function getCouponTypeInitiallyReturnsCouponTypeSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->couponType,
            $this->coupon->getCouponType()
        );
    }

    /**
     * @test
     */
    public function getDiscountInitiallyReturnsDiscountSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->discount,
            $this->coupon->getDiscount()
        );
    }

    /**
     * @test
     */
    public function getTaxClassIdReturnsTaxClassIdSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->taxClassId,
            $this->coupon->getTaxClassId()
        );
    }

    /**
     * @test
     */
    public function isRelativeDiscountInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->coupon->isRelativeDiscount()
        );
    }

    /**
     * @test
     */
    public function getNumberAvailableInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->coupon->getNumberAvailable()
        );
    }

    /**
     * @test
     */
    public function getNumberUsedInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->coupon->getNumberUsed()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableReturnsTrueIfNumberAvailableGreaterThanNumberUsed(): void
    {
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(1);
        self::assertTrue(
            $this->coupon->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableReturnsTrueIfHandleAvailableIsFalseAndNumberAvailableEqualsToNumberUsed(): void
    {
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(10);
        self::assertTrue(
            $this->coupon->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableReturnsFalseIfHandleAvailableIsTrueAndNumberAvailableEqualsToNumberUsed(): void
    {
        $this->coupon->setHandleAvailable(true);
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(10);
        self::assertFalse(
            $this->coupon->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableReturnsTrueIfHandleAvailableIsFalseAndNumberAvailableLessThanNumberUsed(): void
    {
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(11);
        self::assertTrue(
            $this->coupon->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableReturnsFalseIfHandleAvailableIsTrueAndNumberAvailableLessThanNumberUsed(): void
    {
        $this->coupon->setHandleAvailable(true);
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(11);
        self::assertFalse(
            $this->coupon->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getCartMinPriceInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->coupon->getCartMinPrice()
        );
    }

    /**
     * @test
     */
    public function setCartMinPriceSetsMinPrice(): void
    {
        $cartMinPrice = 10.0;

        $this->coupon->setCartMinPrice($cartMinPrice);

        self::assertSame(
            $cartMinPrice,
            $this->coupon->getCartMinPrice()
        );
    }

    /**
     * @test
     */
    public function getIsCombinableInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->coupon->getIsCombinable()
        );
    }

    /**
     * @test
     */
    public function setIsCombinableSetsIsCombinable(): void
    {
        $isCombinable = true;

        $this->coupon->setIsCombinable($isCombinable);

        self::assertTrue(
            $this->coupon->getIsCombinable()
        );
    }
}
