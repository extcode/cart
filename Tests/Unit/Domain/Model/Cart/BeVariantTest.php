<?php

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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\AccessibleObjectInterface;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(BeVariant::class)]
class BeVariantTest extends UnitTestCase
{
    private ProductFactoryInterface $productFactory;

    private BeVariantFactoryInterface $beVariantFactory;

    protected TaxClass $taxClass;

    protected ProductInterface $product;

    protected BeVariantInterface $beVariant;

    protected string $id;

    protected string $title;

    protected string $sku;

    protected int $priceCalcMethod;

    protected float $price;

    protected int $quantity;

    public function setUp(): void
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
    public function constructWithoutTitleThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->beVariantFactory->create(
            1,
            $this->product,
            null,
            'test-variant-sku',
            0,
            1.0,
            1
        );
    }

    #[Test]
    public function constructWithoutSkuThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->beVariantFactory->create(
            1,
            $this->product,
            'Test Variant',
            null,
            0,
            1.0,
            1
        );
    }

    #[Test]
    public function constructWithoutQuantityThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->beVariantFactory->create(
            1,
            $this->product,
            'Test Variant',
            'test-variant-sku',
            0,
            1.0,
            null
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
        $this->expectException(\InvalidArgumentException::class);

        $min = 2;
        $max = 1;

        $this->beVariant->setMax($max);
        $this->beVariant->setMin($min);
    }

    #[Test]
    public function throwsInvalidArgumentExceptionIfMinIsNegativ(): void
    {
        $this->expectException(\InvalidArgumentException::class);

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

        //sets max before because $min and $max are 0 by default
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

        //sets max before because $min and $max are 0 by default
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
        $this->expectException(\InvalidArgumentException::class);

        $min = 2;
        $max = 1;

        //sets max before because $min and $max are 0 by default
        $this->beVariant->setMax($min);
        $this->beVariant->setMin($min);

        $this->beVariant->setMax($max);
    }

    #[Test]
    public function getParentPriceReturnsProductPriceForCalculationMethodZero(): void
    {
        self::markTestSkipped();
        //        self::assertSame(
        //            10.00,
        //            $this->beVariant->getParentPrice()
        //        );
    }

    #[Test]
    public function getParentPriceReturnsZeroPriceForCalculationMethodOne(): void
    {
        self::markTestSkipped();
        //        $this->beVariant->setPriceCalcMethod(1);
        //        self::assertSame(
        //            0.00,
        //            $this->beVariant->getParentPrice()
        //        );
    }

    #[Test]
    public function getParentPriceRespectsTheQuantityDiscountsOfProductsForEachVariant(): void
    {
        self::markTestSkipped();
        //        $quantityDiscounts = [
        //            [
        //                'quantity' => 3,
        //                'price' => 7.00,
        //            ],
        //            [
        //                'quantity' => 4,
        //                'price' => 6.00,
        //            ],
        //            [
        //                'quantity' => 5,
        //                'price' => 5.00,
        //            ],
        //            [
        //                'quantity' => 6,
        //                'price' => 4.00,
        //            ],
        //            [
        //                'quantity' => 7,
        //                'price' => 3.00,
        //            ],
        //            [
        //                'quantity' => 8,
        //                'price' => 2.50,
        //            ],
        //        ];
        //
        //        $this->product->setQuantityDiscounts($quantityDiscounts);
        //
        //        $title = 'Test Variant';
        //        $sku = 'test-variant-sku';
        //        $priceCalcMethod = 0;
        //        $price = 1.00;
        //
        //        $beVariant1 = $this->beVariantFactory->create(
        //            '1',
        //            $this->product,
        //            $title,
        //            $sku,
        //            $priceCalcMethod,
        //            $price,
        //            1
        //        );
        //        $this->product->addBeVariant($beVariant1);
        //
        //        $beVariant2 = $this->beVariantFactory->create(
        //            '2',
        //            $this->product,
        //            $title,
        //            $sku,
        //            $priceCalcMethod,
        //            $price,
        //            3
        //        );
        //        $this->product->addBeVariant($beVariant2);
        //
        //        $beVariant3 = $this->beVariantFactory->create(
        //            '3',
        //            $this->product,
        //            $title,
        //            $sku,
        //            $priceCalcMethod,
        //            $price,
        //            4
        //        );
        //        $this->product->addBeVariant($beVariant3);
        //
        //        self::assertSame(
        //            10.00,
        //            $beVariant1->getParentPrice()
        //        );
        //
        //        self::assertSame(
        //            7.00,
        //            $beVariant2->getParentPrice()
        //        );
        //
        //        self::assertSame(
        //            6.00,
        //            $beVariant3->getParentPrice()
        //        );
    }

    /**
     * Creates a mock object which allows for calling protected methods and access of protected properties.
     *
     * Note: This method has no native return types on purpose, but only PHPDoc return type annotations.
     * The reason is that the combination of "union types with generics in PHPDoc" and "a subset of those types as
     * native types, but without the generics" tends to confuse PhpStorm's static type analysis (which we want to avoid).
     *
     * @template T of object
     * @param class-string<T> $originalClassName name of class to create the mock object of
     * @param string[]|null $methods name of the methods to mock, null for "mock no methods"
     * @param array $arguments arguments to pass to constructor
     * @param string $mockClassName the class name to use for the mock class
     * @param bool $callOriginalConstructor whether to call the constructor
     * @param bool $callOriginalClone whether to call the __clone method
     * @param bool $callAutoload whether to call any autoload function
     *
     * @return MockObject&AccessibleObjectInterface&T a mock of `$originalClassName` with access methods added
     *
     * @throws \InvalidArgumentException
     */
    protected function getAccessibleMock(
        string $originalClassName,
        array|null $methods = [],
        array $arguments = [],
        string $mockClassName = '',
        bool $callOriginalConstructor = true,
        bool $callOriginalClone = true,
        bool $callAutoload = true
    ) {
        $mockBuilder = $this->getMockBuilder($this->buildAccessibleProxy($originalClassName))
            ->onlyMethods($methods)
            ->setConstructorArgs($arguments)
            ->setMockClassName($mockClassName);

        if (!$callOriginalConstructor) {
            $mockBuilder->disableOriginalConstructor();
        }

        if (!$callOriginalClone) {
            $mockBuilder->disableOriginalClone();
        }

        if (!$callAutoload) {
            $mockBuilder->disableAutoload();
        }

        return $mockBuilder->getMock();
    }
}
