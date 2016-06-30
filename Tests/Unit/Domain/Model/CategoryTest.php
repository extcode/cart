<?php

namespace Extcode\Cart\Tests\Domain\Model;

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


class CategoryTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \Extcode\Cart\Domain\Model\Category
     */
    protected $category = null;

    protected function setUp()
    {
        $this->category = new \Extcode\Cart\Domain\Model\Category();
    }

    protected function tearDown()
    {
        unset($this->category);
    }

    /**
     * @test
     */
    public function getCartProductSinglePidReturnsSinglePid()
    {
        $cartProductSinglePid = 123;

        $category = $this->getAccessibleMock(
            \Extcode\Cart\Domain\Model\Category::class,
            array('dummy'),
            array(),
            '',
            false
        );

        $category->_set('cartProductSinglePid', $cartProductSinglePid);

        $this->assertEquals(
            $cartProductSinglePid,
            $category->getCartProductSinglePid()
        );
    }
}
