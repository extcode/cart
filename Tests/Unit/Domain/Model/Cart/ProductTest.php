<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Product;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use InvalidArgumentException;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ProductTest extends UnitTestCase
{
    protected TaxClass $taxClass;

    protected Product $product;

    protected string $productType;

    protected int $productId;

    protected string $title;

    protected string $sku;

    protected float $price;

    protected int $quantity;

    public function setUp(): void
    {
        $this->taxClass = new TaxClass(1, '19', 0.19, 'normal');

        $this->productType = 'simple';
        $this->productId = 1001;
        $this->title = 'Test Product';
        $this->sku = 'test-product-sku';
        $this->price = 10.00;
        $this->quantity = 1;

        $this->product = new Product(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );

        parent::setUp();
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

    /**
     * @test
     */
    public function constructCartProductWithoutProductTypeThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Product(
            null,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );
    }

    /**
     * @test
     */
    public function constructCartProductWithoutProductIdThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Product(
            $this->productType,
            null,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );
    }

    /**
     * @test
     */
    public function constructCartProductWithoutSkuThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Product(
            $this->productType,
            $this->productId,
            null,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );
    }

    /**
     * @test
     */
    public function constructCartProductWithoutTitleThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Product(
            $this->productType,
            $this->productId,
            $this->sku,
            null,
            $this->price,
            $this->taxClass,
            $this->quantity
        );
    }

    /**
     * @test
     */
    public function constructCartProductWithoutPriceThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Product(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            null,
            $this->taxClass,
            $this->quantity
        );
    }

    /**
     * @test
     */
    public function constructCartProductWithoutTaxClassThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Product(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            null,
            $this->quantity
        );
    }

    /**
     * @test
     */
    public function constructCartProductWithoutQuantityThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Product(
            $this->productType,
            $this->productId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            null
        );
    }

    /**
     * @test
     */
    public function getCartProductTypeReturnsProductTypeSetByConstructor(): void
    {
        self::assertSame(
            $this->productType,
            $this->product->getProductType()
        );
    }

    /**
     * @test
     */
    public function getCartProductIdReturnsProductIdSetByConstructor(): void
    {
        self::assertSame(
            $this->productId,
            $this->product->getProductId()
        );
    }

    /**
     * @test
     */
    public function getIdForTableProductReturnsTableProductIdSetIndirectlyByConstructor(): void
    {
        $product = new Product(
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

    /**
     * @test
     */
    public function getSkuReturnsSkuSetByConstructor(): void
    {
        self::assertSame(
            $this->sku,
            $this->product->getSku()
        );
    }

    /**
     * @test
     */
    public function getTitleReturnsTitleSetByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->product->getTitle()
        );
    }

    /**
     * @test
     */
    public function getPriceReturnsPriceSetByConstructor(): void
    {
        self::assertSame(
            $this->price,
            $this->product->getPrice()
        );
    }

    /**
     * @test
     */
    public function getSpecialPriceInitiallyReturnsNull(): void
    {
        self::assertNull(
            $this->product->getSpecialPrice()
        );
    }

    /**
     * @test
     */
    public function setSpecialPriceSetsSpecialPrice(): void
    {
        $price = 10.00;
        $specialPrice = 1.00;

        $product = new Product(
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

    /**
     * @test
     */
    public function getSpecialPriceDiscountForEmptySpecialPriceReturnsDiscount(): void
    {
        $price = 10.00;

        $product = new Product(
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

    /**
     * @test
     */
    public function getSpecialPriceDiscountForZeroPriceReturnsZero(): void
    {
        $price = 0.0;
        $specialPrice = 0.00;

        $product = new Product(
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

    /**
     * @test
     */
    public function getSpecialPriceDiscountForGivenSpecialPriceReturnsPercentageDiscount(): void
    {
        $price = 10.00;
        $specialPrice = 9.00;

        $product = new Product(
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

    /**
     * @test
     */
    public function getBestPriceInitiallyReturnsPrice(): void
    {
        $price = 10.00;

        $product = new Product(
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

    /**
     * @test
     */
    public function getBestPriceReturnsPriceWhenPriceIsLessThanSpecialPrice(): void
    {
        $price = 10.00;
        $specialPrice = 11.00;

        $product = new Product(
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

    /**
     * @test
     */
    public function getBestPriceReturnsSpecialPriceWhenSpecialPriceIsLessThanPrice(): void
    {
        $price = 10.00;
        $specialPrice = 5.00;

        $product = new Product(
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

    /**
     * @test
     */
    public function getQuantityDiscountPriceWithoutQuantityPriceReturnsPrice(): void
    {
        $price = 10.00;

        $product = new Product(
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

    /**
     * @test
     */
    public function getQuantityDiscountPriceWithLowerQuantityReturnsPrice(): void
    {
        $price = 10.00;
        $quantityDiscountPrice = 5.00;
        $quantity = 3;

        $quantityDiscounts = [[
            'quantity' => $quantity+1,
            'price' => $quantityDiscountPrice,
        ]];

        $product = new Product(
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

    /**
     * @test
     */
    public function getQuantityDiscountPriceWithSameQuantityReturnsPriceOfQuantityDiscount(): void
    {
        $price = 10.00;
        $quantityDiscountPrice = 5.00;
        $quantity = 3;

        $quantityDiscounts = [[
            'quantity' => $quantity,
            'price' => $quantityDiscountPrice,
        ]];

        $product = new Product(
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

    /**
     * @test
     */
    public function getQuantityDiscountPriceWithHigherQuantityReturnsPriceOfQuantityDiscount(): void
    {
        $price = 10.00;
        $quantityDiscountPrice = 5.00;
        $quantity = 3;

        $quantityDiscounts = [[
            'quantity' => $quantity-1,
            'price' => $quantityDiscountPrice,
        ]];

        $product = new Product(
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

    /**
     * @test
     */
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

        $product = new Product(
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

    /**
     * @test
     */
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

        $product = new Product(
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

    /**
     * @test
     */
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

        $product = new Product(
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

    /**
     * @test
     */
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

        $product = new Product(
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

    /**
     * @test
     */
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

        $product = new Product(
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

    /**
     * @test
     */
    public function getQuantityReturnsQuantitySetByConstructor(): void
    {
        self::assertSame(
            $this->quantity,
            $this->product->getQuantity()
        );
    }

    /**
     * @test
     */
    public function isNetPriceReturnsFalseSetByDefaultConstructor(): void
    {
        self::assertFalse(
            $this->product->isNetPrice()
        );
    }

    /**
     * @test
     */
    public function isNetPriceReturnsTrueSetByDefaultConstructor(): void
    {
        $net_fixture = new Product(
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

    /**
     * @test
     */
    public function setTitleSetsTitle(): void
    {
        $sku = 'new-test-product-sku';

        $this->product->setSku($sku);

        self::assertSame(
            $sku,
            $this->product->getSku()
        );
    }

    /**
     * @test
     */
    public function setSkuSetsSku(): void
    {
        $title = 'New Test Product';

        $this->product->setTitle($title);

        self::assertSame(
            $title,
            $this->product->getTitle()
        );
    }

    /**
     * @test
     */
    public function getMinNumberInCartReturnsInitialValueMinNumber(): void
    {
        self::assertSame(
            0,
            $this->product->getMinNumberInCart()
        );
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function throwsInvalidArgumentExceptionIfMinNumberIsGreaterThanMaxNumber(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $minNumber = 2;
        $maxNumber = 1;

        $this->product->setMaxNumberInCart($maxNumber);
        $this->product->setMinNumberInCart($minNumber);
    }

    /**
     * @test
     */
    public function throwsInvalidArgumentExceptionIfMinNumberIsNegativ(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $minNumber = -1;
        $maxNumber = 1;

        $this->product->setMaxNumberInCart($maxNumber);
        $this->product->setMinNumberInCart($minNumber);
    }

    /**
     * @test
     */
    public function getMaxNumberInCartReturnsInitialValueMaxNumber(): void
    {
        self::assertSame(
            0,
            $this->product->getMaxNumberInCart()
        );
    }

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function setMaxNumberInCartIfMaxNumerIsGreaterThanMinNumber(): void
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

    /**
     * @test
     */
    public function throwsInvalidArgumentExceptionIfMaxNumberIsLesserThanMinNUmber(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $minNumber = 2;
        $maxNumber = 1;

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);
    }

    /**
     * @test
     */
    public function getQuantityIsLeavingRangeReturnsZeroIfQuantityIsInRange(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 7;

        $this->product = new Product(
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
            0,
            $this->product->getQuantityIsLeavingRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsLeavingRangeReturnsZeroIfQuantityIsEqualToMinimum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 5;

        $this->product = new Product(
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
            0,
            $this->product->getQuantityIsLeavingRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsLeavingRangeReturnsZeroIfQuantityIsEqualToMaximum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 10;

        $this->product = new Product(
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
            0,
            $this->product->getQuantityIsLeavingRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsLeavingRangeReturnsMinusOneIfQuantityIsLessThanMinimum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = new Product(
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
            -1,
            $this->product->getQuantityIsLeavingRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsLeavingRangeReturnsOneIfQuantityIsGreaterThanMaximum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 11;

        $this->product = new Product(
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
            1,
            $this->product->getQuantityIsLeavingRange()
        );
    }

    /**
     * @test
     */
    public function isQuantityInRangeReturnsTrueIfQuantityIsInRange(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 7;

        $this->product = new Product(
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

    /**
     * @test
     */
    public function isQuantityInRangeReturnsTrueIfQuantityIsEqualToMinimum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 5;

        $this->product = new Product(
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

    /**
     * @test
     */
    public function isQuantityInRangeReturnsTrueIfQuantityIsEqualToMaximum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 10;

        $this->product = new Product(
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

    /**
     * @test
     */
    public function isQuantityInRangeReturnsFalseIfQuantityIsLessThanMinimum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = new Product(
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

    /**
     * @test
     */
    public function isQuantityInRangeReturnsFalseIfQuantityIsGreaterThanMaximum(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 11;

        $this->product = new Product(
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

    /**
     * @test
     */
    public function getGrossReturnsZeroIfNumberIsOutOfRange(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = new Product(
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

        $this->product = new Product(
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

    /**
     * @test
     */
    public function getNetReturnsZeroIfNumberIsOutOfRange(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = new Product(
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

        $this->product = new Product(
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

    /**
     * @test
     */
    public function getTaxReturnsZeroIfNumberIsOutOfRange(): void
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = new Product(
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

        $this->product = new Product(
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

    /**
     * @test
     */
    public function isVirtualProductInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->product->isVirtualProduct()
        );
    }

    /**
     * @test
     */
    public function setIsVirtualProductSetsIsVirtualProduct(): void
    {
        $this->product->setIsVirtualProduct(true);

        self::assertTrue(
            $this->product->isVirtualProduct()
        );
    }
}
