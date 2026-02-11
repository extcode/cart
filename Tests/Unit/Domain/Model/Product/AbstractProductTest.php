<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Product\AbstractProduct;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(AbstractProduct::class)]
class AbstractProductTest extends UnitTestCase
{
    protected AbstractProduct $product;

    public function setUp(): void
    {
        parent::setUp();

        $this->product = new class extends AbstractProduct {};
    }

    #[Test]
    public function getSkuInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->product->getSku()
        );
    }

    #[Test]
    public function setSkuSetsSku(): void
    {
        $this->product->setSku('Abstract Product SKU');

        self::assertSame(
            'Abstract Product SKU',
            $this->product->getSku()
        );
    }

    #[Test]
    public function getTitleInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->product->getTitle()
        );
    }

    #[Test]
    public function setTitleSetsTitle(): void
    {
        $this->product->setTitle('Abstract Product Title');

        self::assertSame(
            'Abstract Product Title',
            $this->product->getTitle()
        );
    }

    #[Test]
    public function getTeaserInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->product->getTeaser()
        );
    }

    #[Test]
    public function setTeaserSetsTeaser(): void
    {
        $this->product->setTeaser('Abstract Product Teaser');

        self::assertSame(
            'Abstract Product Teaser',
            $this->product->getTeaser()
        );
    }

    #[Test]
    public function getDescriptionInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->product->getDescription()
        );
    }

    #[Test]
    public function setDescriptionSetsDescription(): void
    {
        $this->product->setDescription('Abstract Product Description');

        self::assertSame(
            'Abstract Product Description',
            $this->product->getDescription()
        );
    }
}
