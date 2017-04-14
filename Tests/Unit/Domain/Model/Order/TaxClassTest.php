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

class TaxClassTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\TaxClass
     */
    protected $taxClass = null;

    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * Value
     *
     * @var string
     */
    protected $value;

    /**
     * Calc
     *
     * @var float
     */
    protected $calc = 0.0;

    /**
     *
     */
    public function setUp()
    {
        $this->title = 'normal';
        $this->value = '19';
        $this->count = 0.19;

        $this->taxClass = new \Extcode\Cart\Domain\Model\Order\TaxClass(
            $this->title,
            $this->value,
            $this->calc
        );
    }

    /**
     * @test
     */
    public function constructTaxClassWithoutTitleThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $title for constructor.',
            1456830910
        );

        new \Extcode\Cart\Domain\Model\Order\TaxClass(
            null,
            $this->value,
            $this->calc
        );
    }

    /**
     * @test
     */
    public function constructTaxClassWithoutValueThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $value for constructor.',
            1456830920
        );

        new \Extcode\Cart\Domain\Model\Order\TaxClass(
            $this->title,
            null,
            $this->calc
        );
    }

    /**
     * @test
     */
    public function constructTaxClassWithoutCalcThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $calc for constructor.',
            1456830930
        );

        new \Extcode\Cart\Domain\Model\Order\TaxClass(
            $this->title,
            $this->value,
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
            $this->taxClass->getTitle()
        );
    }

    /**
     * @test
     */
    public function getValueInitiallyReturnsValueSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->value,
            $this->taxClass->getValue()
        );
    }

    /**
     * @test
     */
    public function getCalcInitiallyReturnsCalcSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->calc,
            $this->taxClass->getCalc()
        );
    }

    /**
     * @test
     */
    public function toArrayReturnsArray()
    {
        $taxClassArray = [
            'title' => $this->title,
            'value' => $this->value,
            'calc' => $this->calc
        ];

        $this->assertEquals(
            $taxClassArray,
            $this->taxClass->toArray()
        );
    }
}
