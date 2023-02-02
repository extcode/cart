<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\ProductAdditional;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ProductAdditionalTest extends UnitTestCase
{
    /**
     * @var string
     */
    protected $additionalType = '';

    /**
     * @var string
     */
    protected $additionalKey = '';

    /**
     * @var string
     */
    protected $additionalValue = '';

    /**
     * @var ProductAdditional
     */
    protected $productAdditional;

    public function setUp(): void
    {
        $this->additionalType = 'additional-type';
        $this->additionalKey = 'additional-key';
        $this->additionalValue = 'additional-value';

        $this->productAdditional = new ProductAdditional(
            $this->additionalType,
            $this->additionalKey,
            $this->additionalValue
        );

        parent::setUp();
    }

    /**
     * @test
     */
    public function constructProductAdditionalWithoutAdditionalTypeThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productAdditional = new ProductAdditional(
            null,
            $this->additionalKey,
            $this->additionalValue
        );
    }

    /**
     * @test
     */
    public function constructProductAdditionalWithoutAdditionalKeyThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productAdditional = new ProductAdditional(
            $this->additionalType,
            null,
            $this->additionalValue
        );
    }

    /**
     * @test
     */
    public function constructProductAdditionalWithoutAdditionalValueThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productAdditional = new ProductAdditional(
            $this->additionalType,
            $this->additionalKey,
            null
        );
    }

    /**
     * @test
     */
    public function getAdditionalTypeInitiallyReturnsAdditionalTypeSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->additionalType,
            $this->productAdditional->getAdditionalType()
        );
    }

    /**
     * @test
     */
    public function getAdditionalKeyInitiallyReturnsAdditionalKeySetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->additionalKey,
            $this->productAdditional->getAdditionalKey()
        );
    }

    /**
     * @test
     */
    public function getAdditionalValueInitiallyReturnsAdditionalValueSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->additionalValue,
            $this->productAdditional->getAdditionalValue()
        );
    }

    /**
     * @test
     */
    public function getAdditionalDataInitiallyReturnsAdditionalDataSetDirectlyByConstructor(): void
    {
        $additionalData = 'additional-data';

        $productAdditional = new ProductAdditional(
            $this->additionalType,
            $this->additionalKey,
            $this->additionalValue,
            $additionalData
        );

        self::assertSame(
            $additionalData,
            $productAdditional->getAdditionalData()
        );
    }

    /**
     * @test
     */
    public function getAdditionalDataInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->productAdditional->getAdditionalData()
        );
    }

    /**
     * @test
     */
    public function setAdditionalDataSetsAdditionalData(): void
    {
        $additionalData = 'additional-data';

        $this->productAdditional->setAdditionalData($additionalData);

        self::assertSame(
            $additionalData,
            $this->productAdditional->getAdditionalData()
        );
    }
}
