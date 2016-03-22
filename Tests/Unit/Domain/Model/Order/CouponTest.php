<?php

namespace Extcode\Cart\Tests\Domain\Model\Order;

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
     * @var \Extcode\Cart\Domain\Model\Order\Coupon
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
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass = null;

    /**
     * Tax
     *
     * @var float
     */
    protected $tax = 0.0;

    /**
     *
     */
    public function setUp()
    {
        $this->taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $this->title = 'Coupon';
        $this->code = 'coupon';
        $this->discount = 10.00;
        $this->tax = 1.60;

        $this->coupon = new \Extcode\Cart\Domain\Model\Order\Coupon(
            $this->title,
            $this->code,
            $this->discount,
            $this->taxClass,
            $this->tax
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
            1455452810
        );

        new \Extcode\Cart\Domain\Model\Order\Coupon(
            null,
            $this->code,
            $this->discount,
            $this->taxClass,
            $this->tax
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
            1455452820
        );

        new \Extcode\Cart\Domain\Model\Order\Coupon(
            $this->title,
            null,
            $this->discount,
            $this->taxClass,
            $this->tax
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
            1455452830
        );

        new \Extcode\Cart\Domain\Model\Order\Coupon(
            $this->title,
            $this->code,
            null,
            $this->taxClass,
            $this->tax
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
            1455452840
        );

        new \Extcode\Cart\Domain\Model\Order\Coupon(
            $this->title,
            $this->code,
            $this->discount,
            null,
            $this->tax
        );
    }

    /**
     * @test
     */
    public function constructCouponWithoutTaxThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $tax for constructor.',
            1455452850
        );

        new \Extcode\Cart\Domain\Model\Order\Coupon(
            $this->title,
            $this->code,
            $this->discount,
            $this->taxClass,
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
    public function getTaxInitiallyReturnsTaxSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->tax,
            $this->coupon->getTax()
        );
    }
}
