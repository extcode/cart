<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Product;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ProductTest extends UnitTestCase
{
    /**
     * @var Product
     */
    protected $product;

    /**
     * @var string
     */
    protected $sku;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var int
     */
    protected $count = 0;

    public function setUp(): void
    {
        $this->sku = 'sku';
        $this->title = 'title';
        $this->count = 1;

        $this->product = new Product(
            $this->sku,
            $this->title,
            $this->count
        );
    }

    /**
     * @test
     */
    public function constructProductWithoutSkuThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->product = new Product(
            null,
            $this->title,
            $this->count
        );
    }

    /**
     * @test
     */
    public function constructProductWithoutTitleThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->product = new Product(
            $this->sku,
            null,
            $this->count
        );
    }

    /**
     * @test
     */
    public function constructProductWithoutCountThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->product = new Product(
            $this->sku,
            $this->title,
            null
        );
    }

    /**
     * @test
     */
    public function getSkuInitiallyReturnsSkuSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->sku,
            $this->product->getSku()
        );
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->product->getTitle()
        );
    }

    /**
     * @test
     */
    public function getCountInitiallyReturnsCountSetDirectlyByConstructor(): void
    {
        self::assertSame(
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
    public function getProductTypeReturnsInitialValueForProductType(): void
    {
        self::assertSame(
            '',
            $this->product->getProductType()
        );
    }

    /**
     * @test
     */
    public function setProductTypeSetsProductType(): void
    {
        $this->product->setProductType('configurable');

        self::assertSame(
            'configurable',
            $this->product->getProductType()
        );
    }
}
