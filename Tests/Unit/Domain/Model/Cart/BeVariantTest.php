<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\BeVariant;
use Extcode\Cart\Domain\Model\Cart\BeVariantFactory;
use Extcode\Cart\Domain\Model\Cart\BeVariantFactoryInterface;
use Extcode\Cart\Domain\Model\Cart\BeVariantInterface;
use Extcode\Cart\Domain\Model\Cart\ProductFactory;
use Extcode\Cart\Domain\Model\Cart\ProductFactoryInterface;
use Extcode\Cart\Domain\Model\Cart\ProductInterface;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(BeVariant::class)]
class BeVariantTest extends UnitTestCase
{
    protected TaxClass $taxClass;

    protected ProductInterface $product;

    protected BeVariantInterface $beVariant;

    protected string $id;

    protected string $title;

    protected string $sku;

    protected int $priceCalcMethod;

    protected float $price;

    protected int $quantity;

    private ProductFactoryInterface $productFactory;

    private BeVariantFactoryInterface $beVariantFactory;

    protected function setUp(): void
    {
        parent::setUp();

        $this->productFactory = GeneralUtility::makeInstance(ProductFactory::class);
        $this->beVariantFactory = GeneralUtility::makeInstance(BeVariantFactory::class);

        $this->taxClass = new TaxClass(1, '19 %', 0.19, 'normal');

        $this->product = $this->productFactory->create(
            'Cart',
            1,
            'SKU',
            'TITLE',
            10.00,
            $this->taxClass,
            1
        );

        $this->id = '1';
        $this->title = 'Test Variant';
        $this->sku = 'test-variant-sku';
        $this->priceCalcMethod = 0;
        $this->price = 1.00;
        $this->quantity = 1;

        $this->beVariant = $this->beVariantFactory->create(
            $this->id,
            $this->product,
            $this->title,
            $this->sku,
            $this->priceCalcMethod,
            $this->price,
            $this->quantity
        );

        $this->product->addBeVariant($this->beVariant);
    }

    #[Test]
    public function getIdReturnsIdSetByConstructor(): void
    {
        self::assertSame(
            $this->id,
            $this->beVariant->getId()
        );
    }

    #[Test]
    public function getSkuReturnsSkuSetByConstructor(): void
    {
        self::assertSame(
            $this->sku,
            $this->beVariant->getSku()
        );
    }

    #[Test]
    public function getCompleteSkuReturnsCompleteSkuSetByConstructor(): void
    {
        $sku = $this->product->getSku() . '-' . $this->sku;
        self::assertSame(
            $sku,
            $this->beVariant->getCompleteSku()
        );
    }

    #[Test]
    public function getTitleReturnsTitleSetByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->beVariant->getTitle()
        );
    }

    #[Test]
    public function getCompleteTitleReturnsCompleteTitleSetByConstructor(): void
    {
        $title = $this->product->getTitle() . ' - ' . $this->title;
        self::assertSame(
            $title,
            $this->beVariant->getCompleteTitle()
        );
    }

    #[Test]
    public function getPriceReturnsPriceSetByConstructor(): void
    {
        self::assertSame(
            $this->price,
            $this->beVariant->getPrice()
        );
    }

    #[Test]
    public function getPriceCalcMethodReturnsPriceCalcSetByConstructor(): void
    {
        self::assertSame(
            $this->priceCalcMethod,
            $this->beVariant->getPriceCalcMethod()
        );
    }

    #[Test]
    public function getQuantityReturnsQuantitySetByConstructor(): void
    {
        self::assertSame(
            $this->quantity,
            $this->beVariant->getQuantity()
        );
    }

    #[Test]
    public function getMinReturnsInitialValueMin(): void
    {
        self::assertSame(
            0,
            $this->beVariant->getMin()
        );
    }

    #[Test]
    public function setMinIfMinIsEqualToMax(): void
    {
        $min = 1;
        $max = 1;

        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);

        self::assertEquals(
            $min,
            $this->beVariant->getMin()
        );
    }

    #[Test]
    public function setMinIfMinIsLesserThanMax(): void
    {
        $min = 1;
        $max = 2;

        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);

        self::assertEquals(
            $min,
            $this->beVariant->getMin()
        );
    }

    #[Test]
    public function throwsInvalidArgumentExceptionIfMinIsGreaterThanMax(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $min = 2;
        $max = 1;

        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);
    }

    #[Test]
    public function throwsInvalidArgumentExceptionIfMinIsNegativ(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $min = -1;
        $max = 1;

        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);
    }

    #[Test]
    public function getMaxReturnsInitialValueMax(): void
    {
        self::assertSame(
            0,
            $this->beVariant->getMax()
        );
    }

    #[Test]
    public function setMaxIfMaxIsEqualToMin(): void
    {
        $min = 1;
        $max = 1;

        // sets max before because $min and $max are 0 by default
        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);

        $this->beVariant->setMax($max);

        self::assertEquals(
            $max,
            $this->beVariant->getMax()
        );
    }

    #[Test]
    public function setMaxIfMaxIsGreaterThanMin(): void
    {
        $min = 1;
        $max = 2;

        // sets max before because $min and $max are 0 by default
        $this->beVariant->setMax($min);
        $this->beVariant->setMin($min);

        $this->beVariant->setMax($max);

        self::assertEquals(
            $max,
            $this->beVariant->getMax()
        );
    }

    #[Test]
    public function throwsInvalidArgumentExceptionIfMaxIsLesserThanMin(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $min = 2;
        $max = 1;

        // sets max before because $min and $max are 0 by default
        $this->beVariant->setMax($min);
        $this->beVariant->setMin($min);

        $this->beVariant->setMax($max);
    }

    #[Test]
    public function getParentPriceReturnsProductPriceForCalculationMethodZero(): void
    {
        self::markTestSkipped();
    }

    #[Test]
    public function getParentPriceReturnsZeroPriceForCalculationMethodOne(): void
    {
        self::markTestSkipped();
    }

    #[Test]
    public function getParentPriceRespectsTheQuantityDiscountsOfProductsForEachVariant(): void
    {
        self::markTestSkipped();
    }
}
