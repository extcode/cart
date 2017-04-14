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

class QuantityDiscountTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * Product Quantity Discount
     *
     * @var \Extcode\Cart\Domain\Model\Product\QuantityDiscount
     */
    protected $fixture = null;

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->fixture = new \Extcode\Cart\Domain\Model\Product\QuantityDiscount();
    }

    /**
     * @test
     */
    public function getPriceInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->fixture->getPrice()
        );
    }

    /**
     * @test
     */
    public function setPriceSetThePrice()
    {
        $price = 1.00;

        $this->fixture->setPrice($price);

        $this->assertSame(
            $price,
            $this->fixture->getPrice()
        );
    }

    /**
     * @test
     */
    public function getQuantityInitiallyReturnsZero()
    {
        $this->assertSame(
            0,
            $this->fixture->getQuantity()
        );
    }

    /**
     * @test
     */
    public function setQuantitySetTheQuantity()
    {
        $quantity = 10;

        $this->fixture->setQuantity($quantity);

        $this->assertSame(
            $quantity,
            $this->fixture->getQuantity()
        );
    }
}
