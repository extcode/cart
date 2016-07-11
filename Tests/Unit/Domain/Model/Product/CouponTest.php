<?php

namespace Extcode\Cart\Tests\Domain\Model\Product;

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

class CouponTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Product\Coupon
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
        $this->discount = 10.00;
        $this->taxClassId = 1;

        $this->coupon = new \Extcode\Cart\Domain\Model\Product\Coupon(
            $this->title,
            $this->code,
            $this->discount,
            $this->taxClassId
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
            1456840910
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Product\Coupon(
            null,
            $this->code,
            $this->discount,
            $this->taxClassId
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
            1456840920
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Product\Coupon(
            $this->title,
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
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $discount for constructor.',
            1456840930
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Product\Coupon(
            $this->title,
            $this->code,
            null,
            $this->taxClassId
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutTaxClassIdThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $taxClassId for constructor.',
            1456840940
        );

        $this->coupon = new \Extcode\Cart\Domain\Model\Product\Coupon(
            $this->title,
            $this->code,
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
    public function setIsCombinableSetsIsCombinable() {
        $isCombinable = true;

        $this->coupon->setIsCombinable($isCombinable);

        $this->assertTrue(
            $this->coupon->getIsCombinable()
        );
    }
}
