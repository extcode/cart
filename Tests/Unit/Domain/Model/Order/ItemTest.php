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
 * Item Test
 *
 * @author Daniel Lorenz
 * @license http://www.gnu.org/licenses/lgpl.html
 *                     GNU Lesser General Public License, version 3 or later
 */
class ItemTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * Cart Pid
     *
     * @var int
     */
    protected $cartPid = 1;

    /**
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $item;

    /**
     *
     */
    public function setUp()
    {
        $this->item = new \Extcode\Cart\Domain\Model\Order\Item(
            $this->cartPid
        );
    }

    /**
     * @test
     */
    public function getCartPidInitiallyReturnsZero()
    {
        $this->assertSame(
            0,
            $this->item->getCartPid()
        );
    }

    /**
     * @test
     */
    public function setCartPidSetsCartPid()
    {
        $this->item->setCartPid('1');

        $this->assertSame(
            '1',
            $this->item->getCartPid()
        );
    }

    /**
     * @test
     */
    public function getCurrencyInitiallyReturnsEuroSignString()
    {
        $this->assertSame(
            '€',
            $this->item->getCurrency()
        );
    }

    /**
     * @test
     */
    public function setCurrencySetsCurrency()
    {
        $this->item->setCurrency('$');

        $this->assertSame(
            '$',
            $this->item->getCurrency()
        );
    }
}
