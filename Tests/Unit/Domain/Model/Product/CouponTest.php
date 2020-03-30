<?php

namespace Extcode\Cart\Tests\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CouponTest extends UnitTestCase
{
    /**
     * Coupon
     *
     * @var \Extcode\Cart\Domain\Model\Coupon
     */
    protected $coupon = null;

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
     * Coupon Type
     *
     * @var string
     */
    protected $couponType = '';

    /**
     * Discount
     *
     * @var float
     */
    protected $discount = 0.0;

    /**
     * TaxClass
     *
     * @var int
     */
    protected $taxClassId = 0;

    /**
     *
     */
    public function setUp()
    {
        $this->title = 'Coupon';
        $this->code = 'coupon';
        $this->couponType = 'cartdiscount';
        $this->discount = 10.00;
        $this->taxClassId = 1;

        $this->coupon = new \Extcode\Cart\Domain\Model\Coupon(
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
    public function constructCouponWithoutTitleThrowsException()
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new \Extcode\Cart\Domain\Model\Coupon(
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
    public function constructCouponWithoutCodeThrowsException()
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new \Extcode\Cart\Domain\Model\Coupon(
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
    public function constructCouponWithoutCouponTypeThrowsException()
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new \Extcode\Cart\Domain\Model\Coupon(
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
    public function constructCouponWithoutDiscountThrowsException()
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new \Extcode\Cart\Domain\Model\Coupon(
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
    public function constructCouponWithoutTaxClassIdThrowsException()
    {
        $this->expectException(\TypeError::class);

        $this->coupon = new \Extcode\Cart\Domain\Model\Coupon(
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
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->title,
            $this->coupon->getTitle()
        );
    }

    /**
     * @test
     */
    public function getCodeInitiallyReturnsCodeSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->code,
            $this->coupon->getCode()
        );
    }

    /**
     * @test
     */
    public function getCouponTypeInitiallyReturnsCouponTypeSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->couponType,
            $this->coupon->getCouponType()
        );
    }

    /**
     * @test
     */
    public function getDiscountInitiallyReturnsDiscountSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->discount,
            $this->coupon->getDiscount()
        );
    }

    /**
     * @test
     */
    public function getTaxClassIdReturnsTaxClassIdSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->taxClassId,
            $this->coupon->getTaxClassId()
        );
    }

    /**
     * @test
     */
    public function isRelativeDiscountInitiallyReturnsFalse()
    {
        $this->assertFalse(
            $this->coupon->isRelativeDiscount()
        );
    }

    /**
     * @test
     */
    public function getNumberAvailableInitiallyReturnsZero()
    {
        $this->assertSame(
            0,
            $this->coupon->getNumberAvailable()
        );
    }

    /**
     * @test
     */
    public function getNumberUsedInitiallyReturnsZero()
    {
        $this->assertSame(
            0,
            $this->coupon->getNumberUsed()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableReturnsTrueIfNumberAvailableGreaterThanNumberUsed()
    {
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(1);
        $this->assertTrue(
            $this->coupon->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableReturnsTrueIfHandleAvailableIsFalseAndNumberAvailableEqualsToNumberUsed()
    {
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(10);
        $this->assertTrue(
            $this->coupon->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableReturnsFalseIfHandleAvailableIsTrueAndNumberAvailableEqualsToNumberUsed()
    {
        $this->coupon->setHandleAvailable(true);
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(10);
        $this->assertFalse(
            $this->coupon->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableReturnsTrueIfHandleAvailableIsFalseAndNumberAvailableLessThanNumberUsed()
    {
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(11);
        $this->assertTrue(
            $this->coupon->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableReturnsFalseIfHandleAvailableIsTrueAndNumberAvailableLessThanNumberUsed()
    {
        $this->coupon->setHandleAvailable(true);
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(11);
        $this->assertFalse(
            $this->coupon->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getCartMinPriceInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->coupon->getCartMinPrice()
        );
    }

    /**
     * @test
     */
    public function setCartMinPriceSetsMinPrice()
    {
        $cartMinPrice = 10.0;

        $this->coupon->setCartMinPrice($cartMinPrice);

        $this->assertSame(
            $cartMinPrice,
            $this->coupon->getCartMinPrice()
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
    public function setIsCombinableSetsIsCombinable()
    {
        $isCombinable = true;

        $this->coupon->setIsCombinable($isCombinable);

        $this->assertTrue(
            $this->coupon->getIsCombinable()
        );
    }
}
