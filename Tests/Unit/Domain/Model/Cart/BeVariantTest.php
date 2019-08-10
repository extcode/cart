<?php

namespace Extcode\Cart\Tests\Domain\Model\Cart;

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
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass = null;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Product
     */
    protected $product = null;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    protected $beVariant = null;

    /**
     * @var string
     */
    protected $id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $sku;

    /**
     * @var int
     */
    protected $priceCalcMethod;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var int
     */
    protected $quantity;

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $this->product = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Product::class)
            ->setMethods(['getBestPrice'])
            ->setConstructorArgs(
                [
                    'Cart',
                    1,
                    0,
                    0,
                    'SKU',
                    'TITLE',
                    10.00,
                    $this->taxClass,
                    1,
                ]
            )->getMock();
        $this->product->expects($this->any())->method('getBestPrice')->will($this->returnValue(10.00));

        $this->id = '1';
        $this->title = 'Test Variant';
        $this->sku = 'test-variant-sku';
        $this->priceCalcMethod = 0;
        $this->price = 1.00;
        $this->quantity = 1;

        $this->beVariant = new \Extcode\Cart\Domain\Model\Cart\BeVariant(
            $this->id,
            $this->product,
            null,
            $this->title,
            $this->sku,
            $this->priceCalcMethod,
            $this->price,
            $this->quantity
        );

        $this->product->addBeVariant($this->beVariant);
    }

    /**
     * @test
     */
    public function getIdReturnsIdSetByConstructor()
    {
        $this->assertSame(
            $this->id,
            $this->beVariant->getId()
        );
    }

    /**
     * @test
     */
    public function getSkuReturnsSkuSetByConstructor()
    {
        $this->assertSame(
            $this->sku,
            $this->beVariant->getSku()
        );
    }

    /**
     * @test
     */
    public function getCompleteSkuReturnsCompleteSkuSetByConstructor()
    {
        $sku = $this->product->getSku() . '-' . $this->sku;
        $this->assertSame(
            $sku,
            $this->beVariant->getCompleteSku()
        );
    }

    /**
     * @test
     */
    public function getSkuWithSkuDelimiterReturnsSkuSetByConstructorWithGivenSkuDelimiter()
    {
        $skuDelimiter = '_';
        $this->beVariant->setSkuDelimiter($skuDelimiter);

        $sku = $this->product->getSku() . $skuDelimiter . $this->sku;
        $this->assertSame(
            $sku,
            $this->beVariant->getCompleteSku()
        );
    }

    /**
     * @test
     */
    public function getTitleReturnsTitleSetByConstructor()
    {
        $this->assertSame(
            $this->title,
            $this->beVariant->getTitle()
        );
    }

    /**
     * @test
     */
    public function getCompleteTitleReturnsCompleteTitleSetByConstructor()
    {
        $title = $this->product->getTitle() . ' - ' . $this->title;
        $this->assertSame(
            $title,
            $this->beVariant->getCompleteTitle()
        );
    }

    /**
     * @test
     */
    public function getTitleWithTitleDelimiterReturnsTitleSetByConstructorWithGivenTitleDelimiter()
    {
        $titleDelimiter = ',';
        $this->beVariant->setTitleDelimiter($titleDelimiter);

        $title = $this->product->getTitle() . $titleDelimiter . $this->title;
        $this->assertSame(
            $title,
            $this->beVariant->getCompleteTitle()
        );
    }

    /**
     * @test
     */
    public function getPriceReturnsPriceSetByConstructor()
    {
        $this->assertSame(
            $this->price,
            $this->beVariant->getPrice()
        );
    }

    /**
     * @test
     */
    public function getPriceCalcMethodReturnsPriceCalcSetByConstructor()
    {
        $this->assertSame(
            $this->priceCalcMethod,
            $this->beVariant->getPriceCalcMethod()
        );
    }

    /**
     * @test
     */
    public function getQuantityReturnsQuantitySetByConstructor()
    {
        $this->assertSame(
            $this->quantity,
            $this->beVariant->getQuantity()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function constructVariantWithoutCartProductOrVariantThrowsInvalidArgumentException()
    {
        new \Extcode\Cart\Domain\Model\Cart\BeVariant(
            $this->id,
            null,
            null,
            $this->sku,
            $this->title,
            $this->priceCalcMethod,
            $this->price,
            $this->quantity
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function constructVariantWithCartProductAndVariantThrowsInvalidArgumentException()
    {
        new \Extcode\Cart\Domain\Model\Cart\BeVariant(
            $this->id,
            $this->product,
            $this->beVariant,
            $this->sku,
            $this->title,
            $this->priceCalcMethod,
            $this->price,
            $this->quantity
        );
    }

    /**
     * @test
     */
    public function constructWithoutTitleThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $title for constructor.',
            1437166475
        );

        new \Extcode\Cart\Domain\Model\Cart\BeVariant(
            1,
            $this->product,
            null,
            null,
            'test-variant-sku',
            0,
            1.0,
            1
        );
    }

    /**
     * @test
     */
    public function constructWithoutSkuThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $sku for constructor.',
            1437166615
        );

        new \Extcode\Cart\Domain\Model\Cart\BeVariant(
            1,
            $this->product,
            null,
            'Test Variant',
            null,
            0,
            1.0,
            1
        );
    }

    /**
     * @test
     */
    public function constructWithoutQuantityThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $quantity for constructor.',
            1437166805
        );

        new \Extcode\Cart\Domain\Model\Cart\BeVariant(
            1,
            $this->product,
            null,
            'Test Variant',
            'test-variant-sku',
            0,
            1.0,
            null
        );
    }

    /**
     * @test
     */
    public function getMinReturnsInitialValueMin()
    {
        $this->assertSame(
            0,
            $this->beVariant->getMin()
        );
    }

    /**
     * @test
     */
    public function setMinIfMinIsEqualToMax()
    {
        $min = 1;
        $max = 1;

        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);

        $this->assertEquals(
            $min,
            $this->beVariant->getMin()
        );
    }

    /**
     * @test
     */
    public function setMinIfMinIsLesserThanMax()
    {
        $min = 1;
        $max = 2;

        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);

        $this->assertEquals(
            $min,
            $this->beVariant->getMin()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function throwsInvalidArgumentExceptionIfMinIsGreaterThanMax()
    {
        $min = 2;
        $max = 1;

        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function throwsInvalidArgumentExceptionIfMinIsNegativ()
    {
        $min = -1;
        $max = 1;

        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);
    }

    /**
     * @test
     */
    public function getMaxReturnsInitialValueMax()
    {
        $this->assertSame(
            0,
            $this->beVariant->getMax()
        );
    }

    /**
     * @test
     */
    public function setMaxIfMaxIsEqualToMin()
    {
        $min = 1;
        $max = 1;

        //sets max before because $min and $max are 0 by default
        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);

        $this->beVariant->setMax($max);

        $this->assertEquals(
            $max,
            $this->beVariant->getMax()
        );
    }

    /**
     * @test
     */
    public function setMaxIfMaxIsGreaterThanMin()
    {
        $min = 1;
        $max = 2;

        //sets max before because $min and $max are 0 by default
        $this->beVariant->setMax($min);
        $this->beVariant->setMin($min);

        $this->beVariant->setMax($max);

        $this->assertEquals(
            $max,
            $this->beVariant->getMax()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function throwsInvalidArgumentExceptionIfMaxIsLesserThanMin()
    {
        $min = 2;
        $max = 1;

        //sets max before because $min and $max are 0 by default
        $this->beVariant->setMax($min);
        $this->beVariant->setMin($min);

        $this->beVariant->setMax($max);
    }

    /**
     * @test
     */
    public function getParentPriceReturnsProductPriceForCalculationMethodZero()
    {
        $this->assertSame(
            10.00,
            $this->beVariant->getParentPrice()
        );
    }

    /**
     * @test
     */
    public function getParentPriceReturnsZeroPriceForCalculationMethodOne()
    {
        $this->beVariant->setPriceCalcMethod(1);
        $this->assertSame(
            0.00,
            $this->beVariant->getParentPrice()
        );
    }

    /**
     * @test
     */
    public function getParentPriceRespectsTheQuantityDiscountsOfProductsForEachVariant()
    {
        $quantityDiscounts = [
            [
                'quantity' => 3,
                'price' => 7.00,
            ],
            [
                'quantity' => 4,
                'price' => 6.00,
            ],
            [
                'quantity' => 5,
                'price' => 5.00,
            ],
            [
                'quantity' => 6,
                'price' => 4.00,
            ],
            [
                'quantity' => 7,
                'price' => 3.00,
            ],
            [
                'quantity' => 8,
                'price' => 2.50,
            ],
        ];

        $product = $this->getAccessibleMock(
            \Extcode\Cart\Domain\Model\Cart\Product::class,
            ['getTaxClass', 'getPrice', 'getTitle', 'getSku'],
            [],
            '',
            false
        );
        $product->_set('quantityDiscounts', $quantityDiscounts);

        $product->expects($this->any())->method('getTaxClass')->will($this->returnValue($this->taxClass));
        $product->expects($this->any())->method('getPrice')->will($this->returnValue(10.00));
        $product->expects($this->any())->method('getTitle')->will($this->returnValue('Test Product'));
        $product->expects($this->any())->method('getSku')->will($this->returnValue('test-product'));

        $title = 'Test Variant';
        $sku = 'test-variant-sku';
        $priceCalcMethod = 0;
        $price = 1.00;

        $beVariant1 = new \Extcode\Cart\Domain\Model\Cart\BeVariant(
            '1',
            $product,
            null,
            $title,
            $sku,
            $priceCalcMethod,
            $price,
            1
        );
        $product->addBeVariant($beVariant1);

        $beVariant2 = new \Extcode\Cart\Domain\Model\Cart\BeVariant(
            '2',
            $product,
            null,
            $title,
            $sku,
            $priceCalcMethod,
            $price,
            3
        );
        $product->addBeVariant($beVariant2);

        $beVariant3 = new \Extcode\Cart\Domain\Model\Cart\BeVariant(
            '3',
            $product,
            null,
            $title,
            $sku,
            $priceCalcMethod,
            $price,
            4
        );
        $product->addBeVariant($beVariant3);

        $this->assertSame(
            10.00,
            $beVariant1->getParentPrice()
        );

        $this->assertSame(
            7.00,
            $beVariant2->getParentPrice()
        );

        $this->assertSame(
            6.00,
            $beVariant3->getParentPrice()
        );
    }
}
