<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Product;
use Extcode\Cart\Domain\Model\Cart\ProductFactory;
use Extcode\Cart\Domain\Model\Cart\ProductFactoryInterface;
use Extcode\Cart\Domain\Model\Cart\ProductInterface;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Product::class)]
class ProductTest extends UnitTestCase
{
    private ProductFactoryInterface $productFactory;

    protected TaxClass $taxClass;

    protected ProductInterface $product;

    protected string $productType;

    protected int $productId;

    protected string $title;

    protected string $sku;

    protected float $price;

    protected int $quantity;

    public function setUp(): void
    {
        parent::setUp();

        $this->productFactory = GeneralUtility::makeInstance(ProductFactory::class);

        $this->taxClass = new TaxClass(1, '19 %', 0.19, 'normal');

        $this->productType = 'simple';
        $this->productId = 1001;
        $this->title = 'Test Product';
        $this->sku = 'test-product-sku';
        $this->price = 10.00;
        $this->quantity = 1;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );
    }

    public function tearDown(): void
    {
        unset(
            $this->product,
            $this->productType,
            $this->productId,
            $this->title,
            $this->sku,
            $this->price,
            $this->quantity,
            $this->taxClass
        );

        parent::tearDown();
    }

    #[Test]
    public function constructCartProductWithoutProductTypeThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productFactory->create(
            null,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );
    }

    #[Test]
    public function constructCartProductWithoutProductIdThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productFactory->create(
            $this->productType,
            null,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );
    }

    #[Test]
    public function constructCartProductWithoutSkuThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productFactory->create(
            $this->productType,
            $this->productId,
            null,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );
    }

    #[Test]
    public function constructCartProductWithoutTitleThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            null,
            $this->price,
            $this->taxClass,
            $this->quantity
        );
    }

    #[Test]
    public function constructCartProductWithoutPriceThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            null,
            $this->taxClass,
            $this->quantity
        );
    }

    #[Test]
    public function constructCartProductWithoutTaxClassThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            null,
            $this->quantity
        );
    }

    #[Test]
    public function constructCartProductWithoutQuantityThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            null
        );
    }

    #[Test]
    public function getCartProductTypeReturnsProductTypeSetByConstructor(): void
    {
        self::assertSame(
            $this->productType,
            $this->product->getProductType()
        );
    }

    #[Test]
    public function getCartProductIdReturnsProductIdSetByConstructor(): void
    {
        self::assertSame(
            $this->productId,
            $this->product->getProductId()
        );
    }

    #[Test]
    public function getIdForTableProductReturnsTableProductIdSetIndirectlyByConstructor(): void
    {
        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );

        self::assertSame(
            $this->productType . '_' . $this->productId,
            $product->getId()
        );
    }

    #[Test]
    public function getSkuReturnsSkuSetByConstructor(): void
    {
        self::assertSame(
            $this->sku,
            $this->product->getSku()
        );
    }

    #[Test]
    public function getTitleReturnsTitleSetByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->product->getTitle()
        );
    }

    #[Test]
    public function getPriceReturnsPriceSetByConstructor(): void
    {
        self::assertSame(
            $this->price,
            $this->product->getPrice()
        );
    }

    #[Test]
    public function getSpecialPriceInitiallyReturnsNull(): void
    {
        self::assertNull(
            $this->product->getSpecialPrice()
        );
    }

    #[Test]
    public function setSpecialPriceSetsSpecialPrice(): void
    {
        $price = 10.00;
        $specialPrice = 1.00;

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setSpecialPrice($specialPrice);

        self::assertSame(
            $specialPrice,
            $product->getSpecialPrice()
        );
    }

    #[Test]
    public function getSpecialPriceDiscountForEmptySpecialPriceReturnsDiscount(): void
    {
        $price = 10.00;

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );

        self::assertSame(
            0.0,
            $product->getSpecialPriceDiscount()
        );
    }

    #[Test]
    public function getSpecialPriceDiscountForZeroPriceReturnsZero(): void
    {
        $price = 0.0;
        $specialPrice = 0.00;

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setSpecialPrice($specialPrice);

        self::assertSame(
            $price,
            $product->getSpecialPriceDiscount()
        );
    }

    #[Test]
    public function getSpecialPriceDiscountForGivenSpecialPriceReturnsPercentageDiscount(): void
    {
        $price = 10.00;
        $specialPrice = 9.00;

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setSpecialPrice($specialPrice);

        self::assertSame(
            $price,
            $product->getSpecialPriceDiscount()
        );
    }

    #[Test]
    public function getBestPriceInitiallyReturnsPrice(): void
    {
        $price = 10.00;

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );

        self::assertSame(
            $price,
            $product->getBestPrice()
        );
    }

    #[Test]
    public function getBestPriceReturnsPriceWhenPriceIsLessThanSpecialPrice(): void
    {
        $price = 10.00;
        $specialPrice = 11.00;

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setSpecialPrice($specialPrice);

        self::assertSame(
            $price,
            $product->getBestPrice()
        );
    }

    #[Test]
    public function getBestPriceReturnsSpecialPriceWhenSpecialPriceIsLessThanPrice(): void
    {
        $price = 10.00;
        $specialPrice = 5.00;

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setSpecialPrice($specialPrice);

        self::assertSame(
            $specialPrice,
            $product->getBestPrice()
        );
    }

    #[Test]
    public function getQuantityDiscountPriceWithoutQuantityPriceReturnsPrice(): void
    {
        $price = 10.00;

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setQuantityDiscounts([]);

        self::assertSame(
            $price,
            $product->getQuantityDiscountPrice()
        );
    }

    #[Test]
    public function getQuantityDiscountPriceWithLowerQuantityReturnsPrice(): void
    {
        $price = 10.00;
        $quantityDiscountPrice = 5.00;
        $quantity = 3;

        $quantityDiscounts = [[
            'quantity' => $quantity + 1,
            'price' => $quantityDiscountPrice,
        ]];

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setQuantityDiscounts($quantityDiscounts);

        self::assertSame(
            $price,
            $product->getQuantityDiscountPrice()
        );
    }

    #[Test]
    public function getQuantityDiscountPriceWithSameQuantityReturnsPriceOfQuantityDiscount(): void
    {
        $price = 10.00;
        $quantityDiscountPrice = 5.00;
        $quantity = 3;

        $quantityDiscounts = [[
            'quantity' => $quantity,
            'price' => $quantityDiscountPrice,
        ]];

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setQuantityDiscounts($quantityDiscounts);

        self::assertSame(
            $quantityDiscountPrice,
            $product->getQuantityDiscountPrice()
        );
    }

    #[Test]
    public function getQuantityDiscountPriceWithHigherQuantityReturnsPriceOfQuantityDiscount(): void
    {
        $price = 10.00;
        $quantityDiscountPrice = 5.00;
        $quantity = 3;

        $quantityDiscounts = [[
            'quantity' => $quantity - 1,
            'price' => $quantityDiscountPrice,
        ]];

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setQuantityDiscounts($quantityDiscounts);

        self::assertSame(
            $quantityDiscountPrice,
            $product->getQuantityDiscountPrice()
        );
    }

    #[Test]
    public function getQuantityDiscountPriceWithHigherQuantityReturnsCorrectPriceOfQuantityDiscountArray(): void
    {
        $price = 10.00;

        $quantity = 5;
        $quantityDiscountPrice = 5.00;

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
                'quantity' => $quantity,
                'price' => $quantityDiscountPrice,
            ],
            [
                'quantity' => 6,
                'price' => 4.00,
            ],
            [
                'quantity' => 7,
                'price' => 3.00,
            ],
        ];

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setQuantityDiscounts($quantityDiscounts);

        self::assertSame(
            $quantityDiscountPrice,
            $product->getQuantityDiscountPrice()
        );
    }

    #[Test]
    public function getQuantityDiscountPriceWithGivenQuantityReturnsCorrectPriceOfQuantityDiscountArray(): void
    {
        $price = 10.00;

        $quantity = 5;
        $quantityDiscountPrice = 5.00;

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
                'quantity' => $quantity,
                'price' => $quantityDiscountPrice,
            ],
            [
                'quantity' => 6,
                'price' => 4.00,
            ],
            [
                'quantity' => 7,
                'price' => 3.00,
            ],
        ];

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setQuantityDiscounts($quantityDiscounts);

        self::assertSame(
            $quantityDiscountPrice,
            $product->getQuantityDiscountPrice($quantity)
        );
    }

    #[Test]
    public function getBestPriceWithSpecialPriceIsLessThanQuantityPriceArrayReturnsSpecialPrice(): void
    {
        $price = 10.00;

        $specialPrice = 4.00;

        $quantity = 5;
        $quantityDiscountPrice = 5.00;

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
                'quantity' => $quantity,
                'price' => $quantityDiscountPrice,
            ],
            [
                'quantity' => 6,
                'price' => 4.00,
            ],
            [
                'quantity' => 7,
                'price' => 3.00,
            ],
        ];

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setSpecialPrice($specialPrice);

        $product->setQuantityDiscounts($quantityDiscounts);

        self::assertSame(
            $specialPrice,
            $product->getBestPrice()
        );
    }

    #[Test]
    public function getBestPriceWithSpecialPriceIsGreaterThanQuantityPriceArrayReturnsCorrectPriceOfQuantityDiscounts(): void
    {
        $price = 10.00;

        $specialPrice = 6.00;

        $quantity = 5;
        $quantityDiscountPrice = 5.00;

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
                'quantity' => $quantity,
                'price' => $quantityDiscountPrice,
            ],
            [
                'quantity' => 6,
                'price' => 4.00,
            ],
            [
                'quantity' => 7,
                'price' => 3.00,
            ],
        ];

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setSpecialPrice($specialPrice);

        $product->setQuantityDiscounts($quantityDiscounts);

        self::assertSame(
            $quantityDiscountPrice,
            $product->getBestPrice()
        );
    }

    #[Test]
    public function getBestPriceWithSpecialPriceIsGreaterThanGivenQuantityPriceArrayReturnsCorrectPriceOfQuantityDiscounts(): void
    {
        $price = 10.00;

        $specialPrice = 6.00;

        $quantity = 5;
        $quantityDiscountPrice = 5.00;

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
                'quantity' => $quantity,
                'price' => $quantityDiscountPrice,
            ],
            [
                'quantity' => 6,
                'price' => 4.00,
            ],
            [
                'quantity' => 7,
                'price' => 3.00,
            ],
        ];

        $product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setSpecialPrice($specialPrice);

        $product->setQuantityDiscounts($quantityDiscounts);

        self::assertSame(
            $quantityDiscountPrice,
            $product->getBestPrice($quantity)
        );
    }

    #[Test]
    public function getQuantityReturnsQuantitySetByConstructor(): void
    {
        self::assertSame(
            $this->quantity,
            $this->product->getQuantity()
        );
    }

    #[Test]
    public function isNetPriceReturnsFalseSetByDefaultConstructor(): void
    {
        self::assertFalse(
            $this->product->isNetPrice()
        );
    }

    #[Test]
    public function isNetPriceReturnsTrueSetByDefaultConstructor(): void
    {
        $net_fixture = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity,
            true
        );

        self::assertTrue(
            $net_fixture->isNetPrice()
        );
    }

    #[Test]
    public function getMinNumberInCartReturnsInitialValueMinNumber(): void
    {
        self::assertSame(
            0,
            $this->product->getMinNumberInCart()
        );
    }

    #[Test]
    public function setMinNumberInCartIfMinNumberIsEqualToMaxNumber(): void
    {
        $minNumber = 1;
        $maxNumber = 1;

        $this->product->setMaxNumberInCart($maxNumber);
        $this->product->setMinNumberInCart($minNumber);

        self::assertEquals(
            $minNumber,
            $this->product->getMinNumberInCart()
        );
    }

    #[Test]
    public function setMinNumberInCartIfMinNumberIsLesserThanMax(): void
    {
        $minNumber = 1;
        $maxNumber = 2;

        $this->product->setMaxNumberInCart($maxNumber);
        $this->product->setMinNumberInCart($minNumber);

        self::assertEquals(
            $minNumber,
            $this->product->getMinNumberInCart()
        );
    }

    #[Test]
    public function throwsInvalidArgumentExceptionIfMinNumberIsGreaterThanMaxNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $minNumber = 2;
        $maxNumber = 1;

        $this->product->setMaxNumberInCart($maxNumber);
        $this->product->setMinNumberInCart($minNumber);
    }

    #[Test]
    public function throwsInvalidArgumentExceptionIfMinNumberIsNegativ(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $minNumber = -1;
        $maxNumber = 1;

        $this->product->setMaxNumberInCart($maxNumber);
        $this->product->setMinNumberInCart($minNumber);
    }

    #[Test]
    public function getMaxNumberInCartReturnsInitialValueMaxNumber(): void
    {
        self::assertSame(
            0,
            $this->product->getMaxNumberInCart()
        );
    }

    #[Test]
    public function setMaxNumberInCartIfMaxNumberIsEqualToMinNumber(): void
    {
        $minNumber = 1;
        $maxNumber = 1;

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertEquals(
            $maxNumber,
            $this->product->getMaxNumberInCart()
        );
    }

    #[Test]
    public function setMaxNumberInCartIfMaxNumberIsGreaterThanMinNumber(): void
    {
        $minNumber = 1;
        $maxNumber = 2;

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertEquals(
            $maxNumber,
            $this->product->getMaxNumberInCart()
        );
    }

    #[Test]
    public function throwsInvalidArgumentExceptionIfMaxNumberIsLesserThanMinNUmber(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $minNumber = 2;
        $maxNumber = 1;

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);
    }

    #[Test]
    public function isQuantityInRangeReturnsTrueIfQuantityIsInRange(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 7;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertTrue(
            $this->product->isQuantityInRange()
        );
    }

    #[Test]
    public function isQuantityInRangeReturnsTrueIfQuantityIsEqualToMinimum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 5;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertTrue(
            $this->product->isQuantityInRange()
        );
    }

    #[Test]
    public function isQuantityInRangeReturnsTrueIfQuantityIsEqualToMaximum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 10;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertTrue(
            $this->product->isQuantityInRange()
        );
    }

    #[Test]
    public function isQuantityInRangeReturnsFalseIfQuantityIsLessThanMinimum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertFalse(
            $this->product->isQuantityInRange()
        );
    }

    #[Test]
    public function isQuantityInRangeReturnsFalseIfQuantityIsGreaterThanMaximum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 11;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertFalse(
            $this->product->isQuantityInRange()
        );
    }

    #[Test]
    public function getGrossReturnsZeroIfNumberIsOutOfRange(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertSame(
            0.0,
            $this->product->getGross()
        );

        $quantity = 11;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertSame(
            0.0,
            $this->product->getGross()
        );
    }

    #[Test]
    public function getNetReturnsZeroIfNumberIsOutOfRange(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertSame(
            0.0,
            $this->product->getNet()
        );

        $quantity = 11;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertSame(
            0.0,
            $this->product->getNet()
        );
    }

    #[Test]
    public function getTaxReturnsZeroIfNumberIsOutOfRange(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertSame(
            0.0,
            $this->product->getTax()
        );

        $quantity = 11;

        $this->product = $this->productFactory->create(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        self::assertSame(
            0.0,
            $this->product->getTax()
        );
    }

    #[Test]
    public function isVirtualProductInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->product->isVirtualProduct()
        );
    }

    #[Test]
    public function setIsVirtualProductSetsIsVirtualProduct(): void
    {
        $this->product->setIsVirtualProduct(true);

        self::assertTrue(
            $this->product->isVirtualProduct()
        );
    }
}
