<?php

namespace Extcode\Cart\Tests\Domain\Model\Product;

/**
 * This file is part of the "cart_products" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Nimut\TestingFramework\TestCase\UnitTestCase;

class BeVariantTest extends UnitTestCase
{
    /**
     * Product Backend Variant
     *
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
    public function getProductInitiallyReturnsNull()
    {
        $this->assertNull(
            $this->beVariant->getProduct()
        );
    }

    /**
     * @test
     */
    public function setProductSetsProduct()
    {
        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $this->beVariant->setProduct($product);

        $this->assertSame(
            $product,
            $this->beVariant->getProduct()
        );
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

    /**
     * @test
     */
    public function getPriceInitiallyReturnsZero()
    {
        $price = 0.0;

        $this->assertSame(
            $price,
            $this->beVariant->getPrice()
        );
    }

    /**
     * @test
     */
    public function setPriceSetsPrice()
    {
        $price = 100.0;

        $this->beVariant->setPrice($price);

        $this->assertSame(
            $price,
            $this->beVariant->getPrice()
        );
    }

    /**
     * @test
     */
    public function getSpecialPricesInitiallyReturnsEmptyObjectStorage()
    {
        $this->assertInstanceOf(
            \TYPO3\CMS\Extbase\Persistence\ObjectStorage::class,
            $this->beVariant->getSpecialPrices()
        );
    }

    /**
     * @test
     */
    public function addSpecialPriceAddsSpecialPrice()
    {
        $specialPrice = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();

        $this->beVariant->addSpecialPrice($specialPrice);

        $this->assertSame(
            $specialPrice,
            $this->beVariant->getBestSpecialPrice()
        );
    }

    /**
     * @test
     */
    public function getBestSpecialPriceWithOneSpecialPriceReturnsSpecialPrice()
    {
        $specialPrice = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();

        $this->beVariant->addSpecialPrice($specialPrice);

        $this->assertSame(
            $specialPrice,
            $this->beVariant->getBestSpecialPrice()
        );
    }

    /**
     * DataProvider for Best Special Price
     *
     * @return array
     */
    public function bestSpecialPriceProvider()
    {
        return [
            [0,  50.0, 100.0,  150.0, 1],
            [0, 100.0,  50.0,  150.0, 2],
            [0, 100.0, 150.0,   50.0, 3],
            [1,  50.0, 100.0,  150.0, 1],
            [1, 100.0,  50.0,  150.0, 2],
            [1, 100.0, 150.0,   50.0, 3],
            [2, 150.0,  50.0,  100.0, 1],
            [2,  50.0, 150.0,  100.0, 2],
            [2, 100.0,  50.0,  150.0, 3],
            [3, 150.0,  50.0,  100.0, 1],
            [3,  50.0, 150.0,  100.0, 2],
            [3, 100.0,  50.0,  150.0, 3],
            [4,  50.0, 100.0,  150.0, 1],
            [4, 100.0,  50.0,  150.0, 2],
            [4, 100.0, 150.0,   50.0, 3],
            [5,  50.0, 100.0,  150.0, 1],
            [5, 100.0,  50.0,  150.0, 2],
            [5, 100.0, 150.0,   50.0, 3],
        ];
    }

    /**
     * @test
     * @dataProvider bestSpecialPriceProvider
     */
    public function getBestSpecialPriceWithMoreSpecialPricesReturnsBestSpecialPrice(
        $priceCalcMethod,
        $priceForSpecialPrice1,
        $priceForSpecialPrice2,
        $priceForSpecialPrice3,
        $expectedBestSpecialPrice
    ) {
        $this->beVariant->setPriceCalcMethod($priceCalcMethod);

        $specialPrice1 = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice1->setPrice($priceForSpecialPrice1);
        $specialPrice2 = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice2->setPrice($priceForSpecialPrice2);
        $specialPrice3 = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
        $specialPrice3->setPrice($priceForSpecialPrice3);

        $this->beVariant->addSpecialPrice($specialPrice1);
        $this->beVariant->addSpecialPrice($specialPrice2);
        $this->beVariant->addSpecialPrice($specialPrice3);

        $specialPrices = [
            1 => $specialPrice1,
            2 => $specialPrice2,
            3 => $specialPrice3,
        ];

        $this->assertSame(
            $specialPrices[$expectedBestSpecialPrice],
            $this->beVariant->getBestSpecialPrice()
        );
    }

    /**
     * @test
     */
    public function getBestPriceInitiallyReturnsPrice()
    {
        $price = 100.0;

        $this->beVariant->setPrice($price);

        $this->assertSame(
            $price,
            $this->beVariant->getBestPrice()
        );
    }

    /**
     * DataProvider for Best Price
     *
     * @return array
     */
    public function bestPriceProvider()
    {
        return [
            [0, 100.0,  50.0,  50.0],
            [0, 100.0, 150.0, 100.0],
            [1, 100.0,  50.0,  50.0],
            [1, 100.0, 150.0, 100.0],
            [2, 100.0,  50.0, 100.0],
            [2, 100.0, 150.0, 150.0],
            [3, 100.0,  50.0, 100.0],
            [3, 100.0, 150.0, 150.0],
            [4, 100.0,  50.0,  50.0],
            [4, 100.0, 150.0, 100.0],
            [5, 100.0,  50.0,  50.0],
            [5, 100.0, 150.0, 100.0],
        ];
    }

    /**
     * @test
     * @dataProvider bestPriceProvider
     */
    public function getBestPriceWithSpecialPriceAndDifferentPriceCalcMethodsReturnsBestSpecialPrice(
        $priceCalcMethod,
        $price,
        $specialPrice,
        $expectedBestPrice
    ) {
        $specialPriceObj = $this->getMock(
            \Extcode\Cart\Domain\Model\Product\SpecialPrice::class,
            [],
            [],
            '',
            false
        );
        $specialPriceObj->expects($this->any())->method('getPrice')->will($this->returnValue($specialPrice));

        $this->beVariant->setPrice($price);
        $this->beVariant->setPriceCalcMethod($priceCalcMethod);
        $this->beVariant->addSpecialPrice($specialPriceObj);

        $this->assertSame(
            $expectedBestPrice,
            $this->beVariant->getBestPrice()
        );
    }

    /**
     * DataProvider for Best Price Calculated
     *
     * @return array
     */
    public function bestPriceCalculatedProvider()
    {
        return [
            [0, 500.0, 400.0, 350.0, 500.0],
            [0, 500.0, 400.0, 450.0, 500.0],
            [1, 500.0, 400.0, 350.0, 350.0],
            [1, 500.0, 400.0, 450.0, 400.0],
            [2, 500.0,  20.0,  15.0, 480.0],
            [2, 500.0,  20.0,  25.0, 475.0],
            [3, 500.0,  20.0,  15.0, 400.0],
            [3, 500.0,  20.0,  25.0, 375.0],
            [4, 500.0,  20.0,  15.0, 515.0],
            [4, 500.0,  20.0,  25.0, 520.0],
            [5, 500.0,  20.0,  15.0, 575.0],
            [5, 500.0,  20.0,  25.0, 600.0],
        ];
    }

    /**
     * @test
     * @dataProvider bestPriceCalculatedProvider
     */
    public function getBestPriceCalculatedWithPriceCalcMethod0ReturnsPrice(
        $priceCalcMethod,
        $productPrice,
        $price,
        $specialPrice,
        $expectedBestPrice
    ) {
        $specialPriceObj = $this->getMock(
            \Extcode\Cart\Domain\Model\Product\SpecialPrice::class,
            [],
            [],
            '',
            false
        );
        $specialPriceObj->expects($this->any())->method('getPrice')->will($this->returnValue($specialPrice));

        $this->beVariant->setPrice($price);
        $this->beVariant->setPriceCalcMethod($priceCalcMethod);
        $this->beVariant->addSpecialPrice($specialPriceObj);

        $product = new \Extcode\Cart\Domain\Model\Product\Product();
        $product->setPrice($productPrice);
        $this->beVariant->setProduct($product);

        $this->assertSame(
            $expectedBestPrice,
            $this->beVariant->getBestPriceCalculated()
        );
    }

    /**
     * @test
     */
    public function getStockInitiallyReturnsZero()
    {
        $this->assertSame(
            0,
            $this->beVariant->getStock()
        );
    }

    /**
     * @test
     */
    public function setStockSetsStock()
    {
        $stock = 10;
        $this->beVariant->setStock($stock);

        $this->assertSame(
            $stock,
            $this->beVariant->getStock()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableInitiallyReturnsFalse()
    {
        $this->assertFalse(
            $this->beVariant->getIsAvailable()
        );
    }

    /**
     * @test
     */
    public function getIsAvailableWithStockGreaterZeroReturnsTrue()
    {
        $this->beVariant->setStock(10);

        $this->assertTrue(
            $this->beVariant->getIsAvailable()
        );
    }
}
