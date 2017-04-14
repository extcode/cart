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

class ProductAdditionalTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * Additional Type
     *
     * @var string
     */
    protected $additionalType = '';

    /**
     * Additional Key
     *
     * @var string
     */
    protected $additionalKey = '';

    /**
     * Additional Value
     *
     * @var string
     */
    protected $additionalValue = '';

    /**
     * @var \Extcode\Cart\Domain\Model\Order\ProductAdditional
     */
    protected $productAdditional = null;

    /**
     *
     */
    public function setUp()
    {
        $this->additionalType = 'additional-type';
        $this->additionalKey = 'additional-key';
        $this->additionalValue = 'additional-value';

        $this->productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            $this->additionalType,
            $this->additionalKey,
            $this->additionalValue
        );
    }

    /**
     * @test
     */
    public function constructProductAdditionalWithoutAdditionalTypeThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $additionalType for constructor.',
            1456828210
        );

        $this->productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            null,
            $this->additionalKey,
            $this->additionalValue
        );
    }

    /**
     * @test
     */
    public function constructProductAdditionalWithoutAdditionalKeyThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $additionalKey for constructor.',
            1456828220
        );

        $this->productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            $this->additionalType,
            null,
            $this->additionalValue
        );
    }

    /**
     * @test
     */
    public function constructProductAdditionalWithoutAdditionalValueThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $additionalValue for constructor.',
            1456828230
        );

        $this->productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            $this->additionalType,
            $this->additionalKey,
            null
        );
    }

    /**
     * @test
     */
    public function getAdditionalTypeInitiallyReturnsAdditionalTypeSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->additionalType,
            $this->productAdditional->getAdditionalType()
        );
    }

    /**
     * @test
     */
    public function getAdditionalKeyInitiallyReturnsAdditionalKeySetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->additionalKey,
            $this->productAdditional->getAdditionalKey()
        );
    }

    /**
     * @test
     */
    public function getAdditionalValueInitiallyReturnsAdditionalValueSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->additionalValue,
            $this->productAdditional->getAdditionalValue()
        );
    }

    /**
     * @test
     */
    public function getAdditionalDataInitiallyReturnsAdditionalDataSetDirectlyByConstructor()
    {
        $additionalData = 'additional-data';

        $productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            $this->additionalType,
            $this->additionalKey,
            $this->additionalValue,
            $additionalData
        );

        $this->assertSame(
            $additionalData,
            $productAdditional->getAdditionalData()
        );
    }

    /**
     * @test
     */
    public function getAdditionalDataInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->productAdditional->getAdditionalData()
        );
    }

    /**
     * @test
     */
    public function setAdditionalDataSetsAdditionalData()
    {
        $additionalData = 'additional-data';

        $this->productAdditional->setAdditionalData($additionalData);

        $this->assertSame(
            $additionalData,
            $this->productAdditional->getAdditionalData()
        );
    }
}
