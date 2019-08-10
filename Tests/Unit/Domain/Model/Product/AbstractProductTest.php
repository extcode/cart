<?php

namespace Extcode\Cart\Tests\Domain\Model\Product;

/**
 * This file is part of the "cart_products" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Nimut\TestingFramework\TestCase\UnitTestCase;

class AbstractProductTest extends UnitTestCase
{
    /**
     * @var \PHPUnit_Framework_MockObject_MockObject
     */
    protected $product = null;

    public function setUp()
    {
        $this->product = $this->getMockForAbstractClass('\Extcode\Cart\Domain\Model\Product\AbstractProduct');
    }

    /**
     * @test
     */
    public function getSkuReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->product->getSku()
        );
    }

    /**
     * @test
     */
    public function setSkuForStringSetsSku()
    {
        $this->product->setSku('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'sku',
            $this->product
        );
    }

    /**
     * @test
     */
    public function getTitleReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->product->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleForStringSetsTitle()
    {
        $this->product->setTitle('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'title',
            $this->product
        );
    }

    /**
     * @test
     */
    public function getDescriptionReturnsInitialValueForString()
    {
        $this->assertSame(
            '',
            $this->product->getDescription()
        );
    }

    /**
     * @test
     */
    public function setDescriptionForStringSetsDescription()
    {
        $this->product->setDescription('Conceived at T3CON10');

        $this->assertAttributeEquals(
            'Conceived at T3CON10',
            'description',
            $this->product
        );
    }
}
