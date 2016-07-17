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


class DiscountTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Discount
     */
    protected $discount = null;

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
     * Gross
     *
     * @var float
     */
    protected $gross = 0.0;

    /**
     * Net
     *
     * @var float
     */
    protected $net = 0.0;

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

        $this->title = 'Discount';
        $this->code = 'discount';
        $this->gross = 10.00;
        $this->net = 8.40;
        $this->tax = 1.60;

        $this->discount = new \Extcode\Cart\Domain\Model\Order\Discount(
            $this->title,
            $this->code,
            $this->gross,
            $this->net,
            $this->taxClass,
            $this->tax
        );
    }

    /**
     * @test
     */
    public function constructDiscountWithoutTitleThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $title for constructor.',
            1455452810
        );

        new \Extcode\Cart\Domain\Model\Order\Discount(
            null,
            $this->code,
            $this->gross,
            $this->net,
            $this->taxClass,
            $this->tax
        );
    }

    /**
     * @test
     */
    public function constructDiscountWithoutCodeThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $code for constructor.',
            1455452820
        );

        new \Extcode\Cart\Domain\Model\Order\Discount(
            $this->title,
            null,
            $this->gross,
            $this->net,
            $this->taxClass,
            $this->tax
        );
    }

    /**
     * @test
     */
    public function constructDiscountWithoutGrossThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $gross for constructor.',
            1468779204
        );

        new \Extcode\Cart\Domain\Model\Order\Discount(
            $this->title,
            $this->code,
            null,
            $this->net,
            $this->taxClass,
            $this->tax
        );
    }

    /**
     * @test
     */
    public function constructDiscountWithoutNetThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $net for constructor.',
            1468779221
        );

        new \Extcode\Cart\Domain\Model\Order\Discount(
            $this->title,
            $this->code,
            $this->gross,
            null,
            $this->taxClass,
            $this->tax
        );
    }

    /**
     * @test
     */
    public function constructDiscountWithoutTaxClassThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $taxClass for constructor.',
            1455452840
        );

        new \Extcode\Cart\Domain\Model\Order\Discount(
            $this->title,
            $this->code,
            $this->gross,
            $this->net,
            null,
            $this->tax
        );
    }

    /**
     * @test
     */
    public function constructDiscountWithoutTaxThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $tax for constructor.',
            1455452850
        );

        new \Extcode\Cart\Domain\Model\Order\Discount(
            $this->title,
            $this->code,
            $this->gross,
            $this->net,
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
            $this->discount->getTitle()
        );
    }

    /**
     * @test
     */
    public function getCodeInitiallyReturnsCodeSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->code,
            $this->discount->getCode()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsGrossSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->gross,
            $this->discount->getGross()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsNetSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->net,
            $this->discount->getNet()
        );
    }

    /**
     * @test
     */
    public function getTaxClassInitiallyReturnsTaxClassSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->taxClass,
            $this->discount->getTaxClass()
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsTaxSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->tax,
            $this->discount->getTax()
        );
    }
}
