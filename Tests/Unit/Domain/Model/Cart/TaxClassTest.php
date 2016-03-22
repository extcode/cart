<?php

namespace Extcode\Cart\Tests\Domain\Model\Cart;

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

/**
 * Tax Class Test
 *
 * @package cart
 * @author Daniel Lorenz
 * @license http://www.gnu.org/licenses/lgpl.html
 *                     GNU Lesser General Public License, version 3 or later
 */
class TaxClassTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $fixture = null;

    /**
     * Id
     *
     * @var int
     */
    private $id;

    /**
     * Value
     *
     * @var string
     */
    private $value;

    /**
     * Calc
     *
     * @var int
     */
    private $calc;

    /**
     * Title
     *
     * @var string
     */
    private $title;

    /**
     * Set Up
     *
     * @return void
     */
    public function setUp()
    {
        $this->id = 1;
        $this->value = '19';
        $this->calc = 0.19;
        $this->title = 'normal Tax';

        $this->fixture = new \Extcode\Cart\Domain\Model\Cart\TaxClass(
            $this->id,
            $this->value,
            $this->calc,
            $this->title
        );
    }

    /**
     * Tear Down
     *
     * @return void
     */
    public function tearDown()
    {
        unset($this->id);
        unset($this->value);
        unset($this->calc);
        unset($this->title);

        unset($this->fixture);
    }

    /**
     * @test
     */
    public function constructTaxClassWithoutIdThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $id for constructor.',
            1413981328
        );

        new \Extcode\Cart\Domain\Model\Cart\TaxClass(
            null,
            $this->value,
            $this->calc,
            $this->title
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
            1413981329
        );

        new \Extcode\Cart\Domain\Model\Cart\TaxClass(
            $this->id,
            null,
            $this->calc,
            $this->title
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
            1413981330
        );

        new \Extcode\Cart\Domain\Model\Cart\TaxClass(
            $this->id,
            $this->value,
            null,
            $this->title
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
            1413981331
        );

        new \Extcode\Cart\Domain\Model\Cart\TaxClass(
            $this->id,
            $this->value,
            $this->calc,
            null
        );
    }

    /**
     * @test
     */
    public function getIdReturnsIdSetByConstructor()
    {
        $this->assertSame(
            $this->id,
            $this->fixture->getId()
        );
    }

    /**
     * @test
     */
    public function getValueReturnsValueSetByConstructor()
    {
        $this->assertSame(
            $this->value,
            $this->fixture->getValue()
        );
    }

    /**
     * @test
     */
    public function getCalcReturnsCalcSetByConstructor()
    {
        $this->assertSame(
            $this->calc,
            $this->fixture->getCalc()
        );
    }

    /**
     * @test
     */
    public function getTitleReturnsNameSetByConstructor()
    {
        $this->assertSame(
            $this->title,
            $this->fixture->getTitle()
        );
    }
}
