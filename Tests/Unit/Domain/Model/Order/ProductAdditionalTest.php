<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\ProductAdditional;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ProductAdditional::class)]
class ProductAdditionalTest extends UnitTestCase
{
    protected string $additionalType = '';

    protected string $additionalKey = '';

    protected string $additionalValue = '';

    protected ProductAdditional $productAdditional;

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

    #[Test]
    public function constructProductAdditionalWithoutAdditionalTypeThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productAdditional = new ProductAdditional(
            null,
            $this->additionalKey,
            $this->additionalValue
        );
    }

    #[Test]
    public function constructProductAdditionalWithoutAdditionalKeyThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productAdditional = new ProductAdditional(
            $this->additionalType,
            null,
            $this->additionalValue
        );
    }

    #[Test]
    public function constructProductAdditionalWithoutAdditionalValueThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productAdditional = new ProductAdditional(
            $this->additionalType,
            $this->additionalKey,
            null
        );
    }

    #[Test]
    public function getAdditionalTypeInitiallyReturnsAdditionalTypeSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->additionalType,
            $this->productAdditional->getAdditionalType()
        );
    }

    #[Test]
    public function getAdditionalKeyInitiallyReturnsAdditionalKeySetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->additionalKey,
            $this->productAdditional->getAdditionalKey()
        );
    }

    #[Test]
    public function getAdditionalValueInitiallyReturnsAdditionalValueSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->additionalValue,
            $this->productAdditional->getAdditionalValue()
        );
    }

    #[Test]
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

    #[Test]
    public function getAdditionalDataInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->productAdditional->getAdditionalData()
        );
    }

    #[Test]
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
