<?php

namespace Extcode\Cart\Tests\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ProductTest extends UnitTestCase
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
        $this->expectException(\TypeError::class);

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
        $this->expectException(\TypeError::class);

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
        $this->expectException(\TypeError::class);

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
