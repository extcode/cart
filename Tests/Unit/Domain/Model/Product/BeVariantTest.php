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


class BeVariantTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Product\BeVariant
     */
    protected $beVariant = null;

    /**
     *
     */
    public function setUp()
    {
        $this->beVariant = new \Extcode\Cart\Domain\Model\Product\BeVariant();
    }

    /**
     * @test
     */
    public function getBeVariantAttributeOption1InitiallyIsNull()
    {
        $this->assertNull(
            $this->beVariant->getBeVariantAttributeOption1()
        );
    }

    /**
     * @test
     */
    public function setBeVariantAttributeOption1SetsBeVariantAttributeOption1()
    {
        $beVariantAttributeOption = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();

        $this->beVariant->setBeVariantAttributeOption1($beVariantAttributeOption);

        $this->assertSame(
            $beVariantAttributeOption,
            $this->beVariant->getBeVariantAttributeOption1()
        );
    }

    /**
     * @test
     */
    public function getBeVariantAttributeOption2InitiallyIsNull()
    {
        $this->assertNull(
            $this->beVariant->getBeVariantAttributeOption2()
        );
    }

    /**
     * @test
     */
    public function setBeVariantAttributeOption2SetsBeVariantAttributeOption2()
    {
        $beVariantAttributeOption = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();

        $this->beVariant->setBeVariantAttributeOption2($beVariantAttributeOption);

        $this->assertSame(
            $beVariantAttributeOption,
            $this->beVariant->getBeVariantAttributeOption2()
        );
    }

    /**
     * @test
     */
    public function getBeVariantAttributeOption3InitiallyIsNull()
    {
        $this->assertNull(
            $this->beVariant->getBeVariantAttributeOption3()
        );
    }

    /**
     * @test
     */
    public function setBeVariantAttributeOption3SetsBeVariantAttributeOption3()
    {
        $beVariantAttributeOption = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();

        $this->beVariant->setBeVariantAttributeOption3($beVariantAttributeOption);

        $this->assertSame(
            $beVariantAttributeOption,
            $this->beVariant->getBeVariantAttributeOption3()
        );
    }
}
