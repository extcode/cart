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


class BeVariantAttributeTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Product\BeVariantAttribute
     */
    protected $beVariantAttribute = null;

    /**
     *
     */
    public function setUp()
    {
        $this->beVariantAttribute = new \Extcode\Cart\Domain\Model\Product\BeVariantAttribute();
    }

    /**
     * @test
     */
    public function getBeVariantAttributeOptionsInitiallyIsEmpty()
    {
        $this->assertEmpty(
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
    }

    /**
     * @test
     */
    public function setTransactionsSetsTransactions()
    {
        $beVariantAttributeOption1 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();
        $beVariantAttributeOption2 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();

        $objectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorage->attach($beVariantAttributeOption1);
        $objectStorage->attach($beVariantAttributeOption2);

        $this->beVariantAttribute->setBeVariantAttributeOptions($objectStorage);

        $this->assertContains(
            $beVariantAttributeOption1,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
        $this->assertContains(
            $beVariantAttributeOption2,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
    }

    /**
     * @test
     */
    public function addTransactionAddsTransaction()
    {
        $beVariantAttributeOption1 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();
        $beVariantAttributeOption2 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();

        $this->beVariantAttribute->addBeVariantAttributeOption($beVariantAttributeOption1);
        $this->beVariantAttribute->addBeVariantAttributeOption($beVariantAttributeOption2);

        $this->assertContains(
            $beVariantAttributeOption1,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
        $this->assertContains(
            $beVariantAttributeOption2,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
    }

    /**
     * @test
     */
    public function removeTransactionRemovesTransaction()
    {
        $beVariantAttributeOption1 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();
        $beVariantAttributeOption2 = new \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption();

        $this->beVariantAttribute->addBeVariantAttributeOption($beVariantAttributeOption1);
        $this->beVariantAttribute->addBeVariantAttributeOption($beVariantAttributeOption2);
        $this->beVariantAttribute->removeBeVariantAttributeOption($beVariantAttributeOption1);

        $this->assertNotContains(
            $beVariantAttributeOption1,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
        $this->assertContains(
            $beVariantAttributeOption2,
            $this->beVariantAttribute->getBeVariantAttributeOptions()
        );
    }
}
