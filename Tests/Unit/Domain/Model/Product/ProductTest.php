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


class ProductTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \Extcode\Cart\Domain\Model\Product\Product
     */
    protected $product = null;

    protected function setUp()
    {
        $this->product = new \Extcode\Cart\Domain\Model\Product\Product();
    }

    protected function tearDown()
    {
        unset($this->product);
    }

    /**
     * DataProvider for best Special Price calculation
     *
     * @return array
     */
    public function bestSpecialPriceProvider()
    {
        return [
            [100.0, 80.0, 75.0, 90.0, 75.0],
            [100.0, 75.0, 90.0, 50.0, 50.0],
            [100.0, 80.0, 60.0, 80.0, 60.0],
        ];
    }

    /**
     * DataProvider for best Special Price Discount calculation
     *
     * @return array
     */
    public function bestSpecialPriceDiscountProvider()
    {
        return [
            [100.0, 80.0, 75.0, 90.0, 25.0],
            [100.0, 75.0, 90.0, 50.0, 50.0],
            [100.0, 80.0, 60.0, 80.0, 40.0],
        ];
    }

    /**
     * @test
     */
    public function getProductTypeReturnsInitialValueForProductType()
    {
        $this->assertSame(
            'simple',
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

    /**
     * @test
     */
    public function getTeaserReturnsInitialValueForTeaser()
    {
        $this->assertSame(
            '',
            $this->product->getTeaser()
        );
    }

    /**
     * @test
     */
    public function setTeaserForStringSetsTeaser()
    {
        $this->product->setTeaser('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'teaser',
            $this->product
        );
    }

    /**
     * @test
     */
    public function getMinNumberInOrderInitiallyReturnsMinNumberInOrder()
    {
        $this->assertSame(
            0,
            $this->product->getMinNumberInOrder()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setNegativeMinNumberThrowsException()
    {
        $minNumber = -10;

        $this->product->setMinNumberInOrder($minNumber);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setMinNumberGreaterThanMaxNumberThrowsException()
    {
        $minNumber = 10;

        $this->product->setMinNumberInOrder($minNumber);
    }

    /**
     * @test
     */
    public function setMinNumberInOrderSetsMinNumberInOrder()
    {
        $minNumber = 10;

        $this->product->setMaxNumberInOrder($minNumber);
        $this->product->setMinNumberInOrder($minNumber);

        $this->assertSame(
            $minNumber,
            $this->product->getMinNumberInOrder()
        );
    }

    /**
     * @test
     */
    public function getMaxNumberInOrderInitiallyReturnsMaxNumberInOrder()
    {
        $this->assertSame(
            0,
            $this->product->getMaxNumberInOrder()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setNegativeMaxNumberThrowsException()
    {
        $maxNumber = -10;

        $this->product->setMaxNumberInOrder($maxNumber);
    }

    /**
     * @test
     */
    public function setMaxNumberInOrderSetsMaxNumberInOrder()
    {
        $maxNumber = 10;

        $this->product->setMaxNumberInOrder($maxNumber);

        $this->assertSame(
            $maxNumber,
            $this->product->getMaxNumberInOrder()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function setMaxNumberLesserThanMinNumberThrowsException()
    {
        $minNumber = 10;
        $maxNumber = 1;

        $this->product->setMaxNumberInOrder($minNumber);
        $this->product->setMinNumberInOrder($minNumber);

        $this->product->setMaxNumberInOrder($maxNumber);
    }

    /**
     * @test
     */
    public function getPriceReturnsInitialValueForFloat()
    {
        $this->assertSame(
            0.0,
            $this->product->getPrice()
        );
    }

    /**
     * @test
     */
    public function setPriceSetsPrice()
    {
        $this->product->setPrice(3.14159265);

        $this->assertSame(
            3.14159265,
            $this->product->getPrice()
        );
    }

    /**
     * @test
     */
    public function getSpecialPricesInitiallyIsEmpty()
    {
        $this->assertEmpty(
            $this->product->getSpecialPrices()
        );
    }

    /**
     * @test
     */
    public function setSpecialPricesSetsSpecialPrices()
    {
        $price = 10.00;

        $specialPrice = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice->setPrice($price);

        $objectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorage->attach($specialPrice);

        $this->product->setSpecialPrices($objectStorage);

        $this->assertContains(
            $specialPrice,
            $this->product->getSpecialPrices()
        );
    }

    /**
     * @test
     */
    public function addSpecialPriceAddsSpecialPrice()
    {
        $price = 10.00;

        $specialPrice = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice->setPrice($price);

        $this->product->addSpecialPrice($specialPrice);

        $this->assertContains(
            $specialPrice,
            $this->product->getSpecialPrices()
        );
    }

    /**
     * @test
     */
    public function removeSpecialPriceRemovesSpecialPrice()
    {
        $price = 10.00;

        $specialPrice = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice->setPrice($price);

        $this->product->addSpecialPrice($specialPrice);
        $this->product->removeSpecialPrice($specialPrice);

        $this->assertEmpty(
            $this->product->getSpecialPrices()
        );
    }

    /**
     * @test
     */
    public function getBestSpecialPriceDiscountForEmptySpecialPriceReturnsDiscount()
    {
        $price = 10.00;

        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setPrice($price);

        $this->assertSame(
            0.0,
            $product->getBestSpecialPriceDiscount()
        );
    }

    /**
     * @test
     * @dataProvider bestSpecialPriceProvider
     */
    public function getBestSpecialPriceForGivenSpecialPricesReturnsBestSpecialPrice(
        $price,
        $special1,
        $special2,
        $special3,
        $expectedBestSpecialPrice
    ) {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setPrice($price);

        $specialPrice1 = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice1->setPrice($special1);
        $product->addSpecialPrice($specialPrice1);

        $specialPrice2 = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice2->setPrice($special2);
        $product->addSpecialPrice($specialPrice2);

        $specialPrice3 = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice3->setPrice($special3);
        $product->addSpecialPrice($specialPrice3);

        $this->assertSame(
            $expectedBestSpecialPrice,
            $product->getBestSpecialPrice()
        );
    }

    /**
     * @test
     */
    public function getBestSpecialPriceDiscountForGivenSpecialPriceReturnsPercentageDiscount()
    {
        $price = 10.0;
        $porductSpecialPrice = 9.0;

        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setPrice($price);

        $specialPrice = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice->setPrice($porductSpecialPrice);

        $product->addSpecialPrice($specialPrice);

        $this->assertSame(
            10.0,
            $product->getBestSpecialPricePercentageDiscount()
        );
    }

    /**
     * @test
     * @dataProvider bestSpecialPriceDiscountProvider
     */
    public function getBestSpecialPriceDiscountForGivenSpecialPricesReturnsBestPercentageDiscount(
        $price,
        $special1,
        $special2,
        $special3,
        $expectedBestSpecialPriceDiscount
    ) {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setPrice($price);

        $specialPrice1 = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice1->setPrice($special1);
        $product->addSpecialPrice($specialPrice1);

        $specialPrice2 = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice2->setPrice($special2);
        $product->addSpecialPrice($specialPrice2);

        $specialPrice3 = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice3->setPrice($special3);
        $product->addSpecialPrice($specialPrice3);

        $this->assertSame(
            $expectedBestSpecialPriceDiscount,
            $product->getBestSpecialPriceDiscount()
        );
    }

    /**
     * @test
     */
    public function getStockWithoutHandleStockInitiallyReturnsIntMax()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();

        $this->assertSame(
            PHP_INT_MAX,
            $product->getStock()
        );
    }

    /**
     * @test
     */
    public function getStockWithHandleStockInitiallyReturnsZero()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setHandleStock(true);

        $this->assertSame(
            0,
            $product->getStock()
        );
    }

    /**
     * @test
     */
    public function setStockWithHandleStockSetsStock()
    {
        $stock = 10;

        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setStock($stock);
        $product->setHandleStock(true);

        $this->assertSame(
            $stock,
            $product->getStock()
        );

        $product->setHandleStock(false);

        $this->assertSame(
            PHP_INT_MAX,
            $product->getStock()
        );
    }

    /**
     * @test
     */
    public function addToStockAddsANumberOfProductsToStock()
    {
        $numberOfProducts = 10;

        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setHandleStock(true);
        $product->addToStock($numberOfProducts);

        $this->assertSame(
            $numberOfProducts,
            $product->getStock()
        );
    }

    /**
     * @test
     */
    public function removeFromStockAddsRemovesANumberOfProductsFromStock()
    {
        $stock = 100;
        $numberOfProducts = 10;

        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setHandleStock(true);
        $product->setStock($stock);
        $product->removeFromStock($numberOfProducts);

        $this->assertSame(
            ($stock - $numberOfProducts),
            $product->getStock()
        );
    }

    /**
     * @test
     */
    public function handleStockInitiallyReturnsFalse()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();

        $this->assertFalse(
            $product->getHandleStock()
        );
    }

    /**
     * @test
     */
    public function setHandleStockSetsHandleStock()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setHandleStock(true);

        $this->assertTrue(
            $product->getHandleStock()
        );
    }

    /**
     * @test
     */
    public function isAvailableInitiallyReturnsTrue()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();

        $this->assertTrue(
            $product->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithHandleStockIsEnabledAndEmptyStockReturnsFalse()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setHandleStock(true);

        $this->assertFalse(
            $product->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithHandleStockIsEnabledAndNotEmptyStockReturnsTrue()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setStock(10);
        $product->setHandleStock(true);

        $this->assertTrue(
            $product->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithHandleStockAndHandleStockInVariantsIsEnabledAndNoBackendVariantsConfiguredReturnsFalse()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setStock(10);
        $product->setHandleStock(true);
        $product->setHandleStockInVariants(true);

        $this->assertFalse(
            $product->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithHandleStockAndHandleStockInVariantsIsEnabledAndBackendVariantConfiguredIsNotAvailableReturnsFalse()
    {
        $productBackendVariant = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\BeVariant',
            array(),
            array(),
            '',
            false
        );
        $productBackendVariant->expects($this->any())->method('getIsAvailable')->will($this->returnValue(false));

        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->addBeVariant($productBackendVariant);
        $product->setStock(10);
        $product->setHandleStock(true);
        $product->setHandleStockInVariants(true);

        $this->assertFalse(
            $product->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithHandleStockAndHandleStockInVariantsIsEnabledAndBackendVariantConfiguredIsAvailableReturnsFalse()
    {
        $productBackendVariant = $this->getMock('Extcode\\Cart\\Domain\\Model\\Product\\BeVariant', array(), array(), '', false);
        $productBackendVariant->expects($this->any())->method('getIsAvailable')->will($this->returnValue(true));

        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->addBeVariant($productBackendVariant);
        $product->setStock(10);
        $product->setHandleStock(true);
        $product->setHandleStockInVariants(true);

        $this->assertTrue(
            $product->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getPriceMeasure()
    {
        $this->assertSame(
            0.0,
            $this->product->getPriceMeasure()
        );
    }

    /**
     * @test
     */
    public function setPriceMeasureSetsPriceMeasure()
    {
        $priceMeasure = 10.99;

        $this->product->setPriceMeasure($priceMeasure);

        $this->assertSame(
            $priceMeasure,
            $this->product->getPriceMeasure()
        );
    }

    /**
     * @test
     */
    public function getPriceMeasureUnit()
    {
        $this->assertSame(
            '',
            $this->product->getPriceMeasureUnit()
        );
    }

    /**
     * @test
     */
    public function setPriceMeasureUnitSetsPriceMeasureUnit()
    {
        $priceMeasureUnit = 'l';

        $this->product->setPriceMeasureUnit($priceMeasureUnit);

        $this->assertSame(
            $priceMeasureUnit,
            $this->product->getPriceMeasureUnit()
        );
    }

    /**
     * @test
     */
    public function getBasePriceMeasureUnit()
    {
        $this->assertSame(
            '',
            $this->product->getBasePriceMeasureUnit()
        );
    }

    /**
     * @test
     */
    public function setBasePriceMeasureUnitSetsBasePriceMeasureUnit()
    {
        $priceBaseMeasureUnit = 'l';

        $this->product->setBasePriceMeasureUnit($priceBaseMeasureUnit);

        $this->assertSame(
            $priceBaseMeasureUnit,
            $this->product->getBasePriceMeasureUnit()
        );
    }

    /**
     * @test
     */
    public function getIsMeasureUnitCompatibilityInitiallyRetrunsFalse()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();

        $this->assertFalse(
            $product->getIsMeasureUnitCompatibility()
        );
    }

    /**
     * @test
     */
    public function getIsMeasureUnitCompatibilityAndNotSetPriceMeasureUnitsRetrunsFalse()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setBasePriceMeasureUnit('l');

        $this->assertFalse(
            $product->getIsMeasureUnitCompatibility()
        );
    }

    /**
     * @test
     */
    public function getIsMeasureUnitCompatibilityAndNotSetBasePriceMeasureUnitsRetrunsFalse()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setPriceMeasureUnit('l');

        $this->assertFalse(
            $product->getIsMeasureUnitCompatibility()
        );
    }

    /**
     * Measurement Units Provider
     *
     * @return array
     */
    public function measureUnitsProvider()
    {
        return [
            ['mg',  'kg', 1000000.0, 1000.0, 1000.0],
            ['g',   'kg', 1000.0,    1000.0, 1.0],
            ['kg',  'kg', 1.0,       1000.0, 0.001],
            ['ml',  'l',  1000.0,    1000.0, 1.0],
            ['cl',  'l',  100.0,     1000.0, 0.1],
            ['l',   'l',  1.0,       1000.0, 0.001],
            ['cbm', 'l',  0.001,     1.0,    0.001],
            ['mm',  'm',  1000.0,    1000.0, 1.0],
            ['cm',  'm',  100.0,     1000.0, 0.1],
            ['m',   'm',  1.0,       2.0,    0.5],
            ['km',  'm',  0.001,     2.0,    0.0005],
            ['m2',  'm2', 1.0,       20.0,   0.05],
        ];
    }

    /**
     * @test
     * @dataProvider measureUnitsProvider
     */
    public function getIsMeasureUnitCompatibilityRetrunsTrueOnSameTypeOfMeasureUnit(
        $sourceMeasureUnit,
        $targetMeasureUnit,
        $factor,
        $priceMeasure,
        $calculatedBasePrice
    ) {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setPriceMeasureUnit($sourceMeasureUnit);
        $product->setBasePriceMeasureUnit($targetMeasureUnit);

        $this->assertTrue(
            $product->getIsMeasureUnitCompatibility()
        );
    }

    /**
     * @test
     * @dataProvider measureUnitsProvider
     */
    public function getMeasureUnitFactorForGivenPriceMeasureUnitAndBasePriceMeasureUnitRetrunsFactor(
        $sourceMeasureUnit,
        $targetMeasureUnit,
        $factor,
        $priceMeasure,
        $calculatedBasePrice
    ) {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setPriceMeasureUnit($sourceMeasureUnit);
        $product->setBasePriceMeasureUnit($targetMeasureUnit);
        $product->setPriceMeasure(1);

        $this->assertSame(
            $factor,
            $product->getMeasureUnitFactor()
        );
    }

    /**
     * @test
     * @dataProvider measureUnitsProvider
     */
    public function getCalculatedBasePriceForGivenPriceMeasureUnitAndBasePriceMeasureUnitRetrunsPrice(
        $sourceMeasureUnit,
        $targetMeasureUnit,
        $factor,
        $priceMeasure,
        $calculatedBasePrice
    ) {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setPriceMeasureUnit($sourceMeasureUnit);
        $product->setBasePriceMeasureUnit($targetMeasureUnit);
        $product->setPriceMeasure($priceMeasure);

        $this->assertSame(
            $calculatedBasePrice,
            $product->getMeasureUnitFactor()
        );
    }

    /**
     * @test
     */
    public function getServiceAttribute1ReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->product->getServiceAttribute1()
        );
    }

    /**
     * @test
     */
    public function setServiceAttribute1SetsServiceAttribute1()
    {
        $serviceAttribute1 = 1.0;

        $this->product->setServiceAttribute1($serviceAttribute1);

        $this->assertSame(
            $serviceAttribute1,
            $this->product->getServiceAttribute1()
        );
    }

    /**
     * @test
     */
    public function getServiceAttribute2ReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->product->getServiceAttribute2()
        );
    }

    /**
     * @test
     */
    public function setServiceAttribute2SetsServiceAttribute2()
    {
        $serviceAttribute2 = 2.0;

        $this->product->setServiceAttribute2($serviceAttribute2);

        $this->assertSame(
            $serviceAttribute2,
            $this->product->getServiceAttribute2()
        );
    }

    /**
     * @test
     */
    public function getServiceAttribute3ReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->product->getServiceAttribute3()
        );
    }

    /**
     * @test
     */
    public function setServiceAttribute3SetsServiceAttribute3()
    {
        $serviceAttribute3 = 3.0;

        $this->product->setServiceAttribute3($serviceAttribute3);

        $this->assertSame(
            $serviceAttribute3,
            $this->product->getServiceAttribute3()
        );
    }

    /**
     * @test
     */
    public function getTaxClassIdInitiallyReturnsTaxClassId()
    {
        $this->assertSame(
            1,
            $this->product->getTaxClassId()
        );
    }

    /**
     * @test
     */
    public function setTaxClassIdSetsTaxClassId()
    {
        $taxClassId = 2;

        $this->product->setTaxClassId($taxClassId);

        $this->assertSame(
            $taxClassId,
            $this->product->getTaxClassId()
        );
    }

    /**
     * @test
     */
    public function getBeVariantAttribute1InitiallyIsNull()
    {
        $this->assertNull(
            $this->product->getBeVariantAttribute1()
        );
    }

    /**
     * @test
     */
    public function setBeVariantAttribute1SetsBeVariantAttribute1()
    {
        $beVariantAttribute = new \Extcode\Cart\Domain\Model\Product\BeVariantAttribute();

        $this->product->setBeVariantAttribute1($beVariantAttribute);

        $this->assertSame(
            $beVariantAttribute,
            $this->product->getBeVariantAttribute1()
        );
    }

    /**
     * @test
     */
    public function getBeVariantAttribute2InitiallyIsNull()
    {
        $this->assertNull(
            $this->product->getBeVariantAttribute2()
        );
    }

    /**
     * @test
     */
    public function setBeVariantAttribute2SetsBeVariantAttribute2()
    {
        $beVariantAttribute = new \Extcode\Cart\Domain\Model\Product\BeVariantAttribute();

        $this->product->setBeVariantAttribute2($beVariantAttribute);

        $this->assertSame(
            $beVariantAttribute,
            $this->product->getBeVariantAttribute2()
        );
    }

    /**
     * @test
     */
    public function getBeVariantAttribute3InitiallyIsNull()
    {
        $this->assertNull(
            $this->product->getBeVariantAttribute3()
        );
    }

    /**
     * @test
     */
    public function setBeVariantAttribute3SetsBeVariantAttribute3()
    {
        $beVariantAttribute = new \Extcode\Cart\Domain\Model\Product\BeVariantAttribute();

        $this->product->setBeVariantAttribute3($beVariantAttribute);

        $this->assertSame(
            $beVariantAttribute,
            $this->product->getBeVariantAttribute3()
        );
    }

    /**
     * @test
     */
    public function getVariantsInitiallyIsEmpty()
    {
        $this->assertEmpty(
            $this->product->getBeVariants()
        );
    }

    /**
     * @test
     */
    public function setVariantsSetsVariants()
    {
        $variant = new \Extcode\Cart\Domain\Model\Product\BeVariant();

        $objectStorage = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $objectStorage->attach($variant);

        $this->product->setBeVariants($objectStorage);

        $this->assertContains(
            $variant,
            $this->product->getBeVariants()
        );
    }

    /**
     * @test
     */
    public function addVariantAddsVariant()
    {
        $variant = new \Extcode\Cart\Domain\Model\Product\BeVariant();

        $this->product->addBeVariant($variant);

        $this->assertContains(
            $variant,
            $this->product->getBeVariants()
        );
    }

    /**
     * @test
     */
    public function removeVariantRemovesVariant()
    {
        $variant = new \Extcode\Cart\Domain\Model\Product\BeVariant();

        $this->product->addBeVariant($variant);
        $this->product->removeBeVariant($variant);

        $this->assertEmpty(
            $this->product->getBeVariants()
        );
    }
}
