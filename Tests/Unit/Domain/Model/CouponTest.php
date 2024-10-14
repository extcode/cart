<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Coupon;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Coupon::class)]
class CouponTest extends UnitTestCase
{
    protected Coupon $coupon;

    protected string $title = '';

    protected string $code = '';

    protected string $couponType = '';

    protected float $discount = 0.0;

    protected int $taxClassId = 0;

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

        parent::setUp();
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->coupon->getTitle()
        );
    }

    #[Test]
    public function getCodeInitiallyReturnsCodeSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->code,
            $this->coupon->getCode()
        );
    }

    #[Test]
    public function getCouponTypeInitiallyReturnsCouponTypeSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->couponType,
            $this->coupon->getCouponType()
        );
    }

    #[Test]
    public function getDiscountInitiallyReturnsDiscountSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->discount,
            $this->coupon->getDiscount()
        );
    }

    #[Test]
    public function getTaxClassIdReturnsTaxClassIdSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->taxClassId,
            $this->coupon->getTaxClassId()
        );
    }

    #[Test]
    public function isRelativeDiscountInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->coupon->isRelativeDiscount()
        );
    }

    #[Test]
    public function getNumberAvailableInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->coupon->getNumberAvailable()
        );
    }

    #[Test]
    public function getNumberUsedInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->coupon->getNumberUsed()
        );
    }

    #[Test]
    public function setNumberUsedSetsNumberUsed(): void
    {
        $this->coupon->setNumberUsed(17);

        self::assertSame(
            17,
            $this->coupon->getNumberUsed()
        );
    }

    #[Test]
    public function incNumberUsedIncreaseNumberUsedByOne(): void
    {
        $this->coupon->setNumberUsed(17);
        $this->coupon->incNumberUsed();

        self::assertSame(
            18,
            $this->coupon->getNumberUsed()
        );
    }

    #[Test]
    public function isHandleAvailabilityEnabledInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->coupon->isHandleAvailabilityEnabled()
        );
    }

    #[Test]
    public function setIsHandleAvailabilityEnabledSetsHandleAvailable(): void
    {
        $this->coupon->setIsHandleAvailabilityEnabled(true);

        self::assertTrue(
            $this->coupon->isHandleAvailabilityEnabled()
        );
    }

    #[Test]
    public function isAvailableReturnsTrueIfNumberAvailableGreaterThanNumberUsed(): void
    {
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(1);
        self::assertTrue(
            $this->coupon->isAvailable()
        );
    }

    #[Test]
    public function isAvailableReturnsTrueIfHandleAvailableIsFalseAndNumberAvailableEqualsToNumberUsed(): void
    {
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(10);
        self::assertTrue(
            $this->coupon->isAvailable()
        );
    }

    #[Test]
    public function isAvailableReturnsFalseIfHandleAvailableIsTrueAndNumberAvailableEqualsToNumberUsed(): void
    {
        $this->coupon->setIsHandleAvailabilityEnabled(true);
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(10);
        self::assertFalse(
            $this->coupon->isAvailable()
        );
    }

    #[Test]
    public function isAvailableReturnsTrueIfHandleAvailableIsFalseAndNumberAvailableLessThanNumberUsed(): void
    {
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(11);
        self::assertTrue(
            $this->coupon->isAvailable()
        );
    }

    #[Test]
    public function isAvailableReturnsFalseIfHandleAvailableIsTrueAndNumberAvailableLessThanNumberUsed(): void
    {
        $this->coupon->setIsHandleAvailabilityEnabled(true);
        $this->coupon->setNumberAvailable(10);
        $this->coupon->setNumberUsed(11);
        self::assertFalse(
            $this->coupon->isAvailable()
        );
    }

    #[Test]
    public function getCartMinPriceInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->coupon->getCartMinPrice()
        );
    }

    #[Test]
    public function setCartMinPriceSetsMinPrice(): void
    {
        $cartMinPrice = 10.0;

        $this->coupon->setCartMinPrice($cartMinPrice);

        self::assertSame(
            $cartMinPrice,
            $this->coupon->getCartMinPrice()
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
    public function setIsCombinableSetsIsCombinable(): void
    {
        $this->coupon->setIsCombinable(true);

        self::assertTrue(
            $this->coupon->isCombinable()
        );
    }
}
