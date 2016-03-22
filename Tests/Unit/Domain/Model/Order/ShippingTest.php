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

/**
 * Shipping Test
 *
 * @package cart
 * @author Daniel Lorenz
 * @license http://www.gnu.org/licenses/lgpl.html
 *                     GNU Lesser General Public License, version 3 or later
 */
class ShippingTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Shipping
     */
    protected $shipping;

    /**
     *
     */
    public function setUp()
    {
        $this->shipping = new \Extcode\Cart\Domain\Model\Order\Shipping();
    }

    /**
     * @test
     */
    public function getNameInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->shipping->getName()
        );
    }

    /**
     * @test
     */
    public function setNameSetsName()
    {
        $this->shipping->setName('foo bar');

        $this->assertSame(
            'foo bar',
            $this->shipping->getName()
        );
    }

    /**
     * @test
     */
    public function getStatusInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            'open',
            $this->shipping->getStatus()
        );
    }

    /**
     * @test
     */
    public function setStatusSetsStatus()
    {
        $this->shipping->setStatus('shipped');

        $this->assertSame(
            'shipped',
            $this->shipping->getStatus()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->shipping->getNet()
        );
    }

    /**
     * @test
     */
    public function setNetSetsNet()
    {
        $this->shipping->setNet(1234.56);

        $this->assertSame(
            1234.56,
            $this->shipping->getNet()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->shipping->getGross()
        );
    }

    /**
     * @test
     */
    public function setGrossSetsGross()
    {
        $this->shipping->setGross(1234.56);

        $this->assertSame(
            1234.56,
            $this->shipping->getGross()
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->shipping->getTax()
        );
    }

    /**
     * @test
     */
    public function setTaxSetsTax()
    {
        $this->shipping->setTax(1234.56);

        $this->assertSame(
            1234.56,
            $this->shipping->getTax()
        );
    }

    /**
     * @test
     */
    public function getNoteInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->shipping->getNote()
        );
    }

    /**
     * @test
     */
    public function setNoteSetsNote()
    {
        $this->shipping->setNote('foo bar');

        $this->assertSame(
            'foo bar',
            $this->shipping->getNote()
        );
    }
}
