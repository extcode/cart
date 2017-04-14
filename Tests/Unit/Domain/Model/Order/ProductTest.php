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

class ProductTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Product
     */
    protected $product = null;

    /**
     * Sku
     *
     * @var string
     */
    protected $sku;

    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * Count
     *
     * @var int
     */
    protected $count = 0;

    /**
     *
     */
    public function setUp()
    {
        $this->sku = 'sku';
        $this->title = 'title';
        $this->count = 1;

        $this->product = new \Extcode\Cart\Domain\Model\Order\Product(
            $this->sku,
            $this->title,
            $this->count
        );
    }

    /**
     * @test
     */
    public function constructProductWithoutSkuThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $sku for constructor.',
            1456830010
        );

        $this->product = new \Extcode\Cart\Domain\Model\Order\Product(
            null,
            $this->title,
            $this->count
        );
    }

    /**
     * @test
     */
    public function constructProductWithoutTitleThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $title for constructor.',
            1456830020
        );

        $this->product = new \Extcode\Cart\Domain\Model\Order\Product(
            $this->sku,
            null,
            $this->count
        );
    }

    /**
     * @test
     */
    public function constructProductWithoutCountThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $count for constructor.',
            1456830030
        );

        $this->product = new \Extcode\Cart\Domain\Model\Order\Product(
            $this->sku,
            $this->title,
            null
        );
    }

    /**
     * @test
     */
    public function getSkuInitiallyReturnsSkuSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->sku,
            $this->product->getSku()
        );
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->title,
            $this->product->getTitle()
        );
    }

    /**
     * @test
     */
    public function getCountInitiallyReturnsCountSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->count,
            $this->product->getCount()
        );
    }

    /**
     * @test
     */
    /**
     * @test
     */
    public function getProductTypeReturnsInitialValueForProductType()
    {
        $this->assertSame(
            '',
            $this->product->getProductType()
        );
    }

    /**
     * @test
     */
    public function setProductTypeSetsProductType()
    {
        $this->product->setProductType('configurable');

        $this->assertSame(
            'configurable',
            $this->product->getProductType()
        );
    }
}
