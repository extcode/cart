<?php

namespace Extcode\Cart\Tests\Domain\Model\Cart;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2015 Daniel Lorenz <ext.cart@extco.de>, extco.de
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Product Test
 *
 * @author Daniel Lorenz
 * @license http://www.gnu.org/licenses/lgpl.html
 *                     GNU Lesser General Public License, version 3 or later
 */
class ProductTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
{

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass = null;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Product
     */
    protected $product = null;

    /**
     * @var string
     */
    protected $productType;

    /**
     * @var int
     */
    protected $productId;

    /**
     * @var int
     */
    protected $tableId;

    /**
     * @var int
     */
    protected $contentId;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $sku;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var int
     */
    protected $quantity;

    /**
     *
     */
    public function setUp()
    {
        $this->taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $this->productType = 'simple';
        $this->productId = 1001;
        $this->tableId = 1002;
        $this->contentId = 1003;
        $this->title = 'Test Product';
        $this->sku = 'test-product-sku';
        $this->price = 10.00;
        $this->quantity = 1;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );
    }

    /**
     *
     */
    public function tearDown()
    {
        unset($this->product);

        unset($this->productId);
        unset($this->tableId);
        unset($this->contentId);
        unset($this->title);
        unset($this->sku);
        unset($this->price);
        unset($this->quantity);

        unset($this->taxClass);
    }

    /**
     * @test
     */
    public function constructCartProductWithoutProductTypeThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $productType for constructor.',
            1468754400
        );

        new \Extcode\Cart\Domain\Model\Cart\Product(
            null,
            $this->productId,
            $this->tableId,
            $this->contentId,
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
    public function constructCartProductWithoutProductIdThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $productId for constructor.',
            1413999100
        );

        new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            null,
            $this->tableId,
            $this->contentId,
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
    public function constructCartProductWithoutSkuThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $sku for constructor.',
            1413999110
        );

        new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
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
    public function constructCartProductWithoutTitleThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $title for constructor.',
            1413999120
        );

        new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
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
    public function constructCartProductWithoutPriceThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $price for constructor.',
            1413999130
        );

        new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
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
    public function constructCartProductWithoutTaxClassThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $taxClass for constructor.',
            1413999140
        );

        new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
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
    public function constructCartProductWithoutQuantityThrowsException()
    {
        $this->setExpectedException(
            'InvalidArgumentException',
            'You have to specify a valid $quantity for constructor.',
            1413999150
        );

        new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
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
    public function getCartProductTypeReturnsProductTypeSetByConstructor()
    {
        $this->assertSame(
            $this->productType,
            $this->product->getProductType()
        );
    }

    /**
     * @test
     */
    public function getCartProductIdReturnsProductIdSetByConstructor()
    {
        $this->assertSame(
            $this->productId,
            $this->product->getProductId()
        );
    }

    /**
     * @test
     */
    public function getTableIdReturnsTableIdSetByConstructor()
    {
        $this->assertSame(
            $this->tableId,
            $this->product->getTableId()
        );
    }

    /**
     * @test
     */
    public function getIdForTableProductReturnsTableProductIdSetIndirectlyByConstructor()
    {
        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            null,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );

        $this->assertSame(
            't_' . $this->tableId . '_' . $this->productId,
            $product->getId()
        );
    }

    /**
     * @test
     */
    public function getIdForFlexformProductReturnsTableProductIdSetIndirectlyByConstructor()
    {
        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity
        );

        $this->assertSame(
            'c_' . $this->contentId . '_' . $this->productId,
            $product->getId()
        );
    }

    /**
     * @test
     */
    public function getContentIdReturnsContentIdSetByConstructor()
    {
        $this->assertSame(
            $this->contentId,
            $this->product->getContentId()
        );
    }

    /**
     * @test
     */
    public function getSkuReturnsSkuSetByConstructor()
    {
        $this->assertSame(
            $this->sku,
            $this->product->getSku()
        );
    }

    /**
     * @test
     */
    public function getTitleReturnsTitleSetByConstructor()
    {
        $this->assertSame(
            $this->title,
            $this->product->getTitle()
        );
    }

    /**
     * @test
     */
    public function getPriceReturnsPriceSetByConstructor()
    {
        $this->assertSame(
            $this->price,
            $this->product->getPrice()
        );
    }

    /**
     * @test
     */
    public function getSpecialPriceInitiallyReturnsNull()
    {
        $this->assertSame(
            null,
            $this->product->getSpecialPrice()
        );
    }

    /**
     * @test
     */
    public function setSpecialPriceSetsSpecialPrice()
    {
        $price = 10.00;
        $specialPrice = 1.00;

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setSpecialPrice($specialPrice);

        $this->assertSame(
            $specialPrice,
            $product->getSpecialPrice()
        );
    }

    /**
     * @test
     */
    public function getSpecialPriceDiscountForEmptySpecialPriceReturnsDiscount()
    {
        $price = 10.00;

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );

        $this->assertSame(
            0.0,
            $product->getSpecialPriceDiscount()
        );
    }

    /**
     * @test
     */
    public function getSpecialPriceDiscountForZeroPriceReturnsZero()
    {
        $price = 0.0;
        $specialPrice = 0.00;

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setSpecialPrice($specialPrice);

        $this->assertSame(
            $price,
            $product->getSpecialPriceDiscount()
        );
    }

    /**
     * @test
     */
    public function getSpecialPriceDiscountForGivenSpecialPriceReturnsPercentageDiscount()
    {
        $price = 10.00;
        $specialPrice = 9.00;

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setSpecialPrice($specialPrice);

        $this->assertSame(
            $price,
            $product->getSpecialPriceDiscount()
        );
    }

    /**
     * @test
     */
    public function getBestPriceInitiallyReturnsPrice()
    {
        $price = 10.00;

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );

        $this->assertSame(
            $price,
            $product->getBestPrice()
        );
    }

    /**
     * @test
     */
    public function getBestPriceReturnsPriceWhenPriceIsLessThanSpecialPrice()
    {
        $price = 10.00;
        $specialPrice = 11.00;

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setSpecialPrice($specialPrice);

        $this->assertSame(
            $price,
            $product->getBestPrice()
        );
    }

    /**
     * @test
     */
    public function getBestPriceReturnsSpecialPriceWhenSpecialPriceIsLessThanPrice()
    {
        $price = 10.00;
        $specialPrice = 5.00;

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setSpecialPrice($specialPrice);

        $this->assertSame(
            $specialPrice,
            $product->getBestPrice()
        );
    }

    /**
     * @test
     */
    public function getQuantityDiscountPriceWithoutQuantityPriceReturnsPrice()
    {
        $price = 10.00;

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $this->quantity
        );
        $product->setQuantityDiscounts([]);

        $this->assertSame(
            $price,
            $product->getQuantityDiscountPrice()
        );
    }

    /**
     * @test
     */
    public function getQuantityDiscountPriceWithLowerQuantityReturnsPrice()
    {
        $price = 10.00;
        $quantityDiscountPrice = 5.00;
        $quantity = 3;

        $quantityDiscounts = [[
            'quantity' => $quantity+1,
            'price' => $quantityDiscountPrice,
        ]];

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setQuantityDiscounts($quantityDiscounts);

        $this->assertSame(
            $price,
            $product->getQuantityDiscountPrice()
        );
    }

    /**
     * @test
     */
    public function getQuantityDiscountPriceWithSameQuantityReturnsPriceOfQuantityDiscount()
    {
        $price = 10.00;
        $quantityDiscountPrice = 5.00;
        $quantity = 3;

        $quantityDiscounts = [[
            'quantity' => $quantity,
            'price' => $quantityDiscountPrice,
        ]];

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setQuantityDiscounts($quantityDiscounts);

        $this->assertSame(
            $quantityDiscountPrice,
            $product->getQuantityDiscountPrice()
        );
    }

    /**
     * @test
     */
    public function getQuantityDiscountPriceWithHigherQuantityReturnsPriceOfQuantityDiscount()
    {
        $price = 10.00;
        $quantityDiscountPrice = 5.00;
        $quantity = 3;

        $quantityDiscounts = [[
            'quantity' => $quantity-1,
            'price' => $quantityDiscountPrice,
        ]];

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setQuantityDiscounts($quantityDiscounts);

        $this->assertSame(
            $quantityDiscountPrice,
            $product->getQuantityDiscountPrice()
        );
    }

    /**
     * @test
     */
    public function getQuantityDiscountPriceWithHigherQuantityReturnsCorrectPriceOfQuantityDiscountArray()
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

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setQuantityDiscounts($quantityDiscounts);

        $this->assertSame(
            $quantityDiscountPrice,
            $product->getQuantityDiscountPrice()
        );
    }

    /**
     * @test
     */
    public function getQuantityDiscountPriceWithGivenQuantityReturnsCorrectPriceOfQuantityDiscountArray()
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

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setQuantityDiscounts($quantityDiscounts);

        $this->assertSame(
            $quantityDiscountPrice,
            $product->getQuantityDiscountPrice($quantity)
        );
    }

    /**
     * @test
     */
    public function getBestPriceWithSpecialPriceIsLessThanQuantityPriceArrayReturnsSpecialPrice()
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

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setSpecialPrice($specialPrice);

        $product->setQuantityDiscounts($quantityDiscounts);

        $this->assertSame(
            $specialPrice,
            $product->getBestPrice()
        );
    }

    /**
     * @test
     */
    public function getBestPriceWithSpecialPriceIsGreaterThanQuantityPriceArrayReturnsCorrectPriceOfQuantityDiscounts()
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

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setSpecialPrice($specialPrice);

        $product->setQuantityDiscounts($quantityDiscounts);

        $this->assertSame(
            $quantityDiscountPrice,
            $product->getBestPrice()
        );
    }

    /**
     * @test
     */
    public function getBestPriceWithSpecialPriceIsGreaterThanGivenQuantityPriceArrayReturnsCorrectPriceOfQuantityDiscounts()
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

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            null,
            $this->contentId,
            $this->sku,
            $this->title,
            $price,
            $this->taxClass,
            $quantity
        );

        $product->setSpecialPrice($specialPrice);

        $product->setQuantityDiscounts($quantityDiscounts);

        $this->assertSame(
            $quantityDiscountPrice,
            $product->getBestPrice($quantity)
        );
    }

    /**
     * @test
     */
    public function getQuantityReturnsQuantitySetByConstructor()
    {
        $this->assertSame(
            $this->quantity,
            $this->product->getQuantity()
        );
    }

    /**
     * @test
     */
    public function getIsNetPriceReturnsFalseSetByDefaultConstructor()
    {
        $this->assertSame(
            false,
            $this->product->getIsNetPrice()
        );
    }

    /**
     * @test
     */
    public function getIsNetPriceReturnsTrueSetByDefaultConstructor()
    {
        $net_fixture = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $this->quantity,
            true
        );

        $this->assertSame(
            true,
            $net_fixture->getIsNetPrice()
        );
    }

    /**
     * @test
     */
    public function setTitleSetsTitle()
    {
        $sku = 'new-test-product-sku';

        $this->product->setSku($sku);

        $this->assertSame(
            $sku,
            $this->product->getSku()
        );
    }

    /**
     * @test
     */
    public function setSkuSetsSku()
    {
        $title = 'New Test Product';

        $this->product->setTitle($title);

        $this->assertSame(
            $title,
            $this->product->getTitle()
        );
    }

    /**
     * @test
     */
    public function getMinNumberInCartReturnsInitialValueMinNumber()
    {
        $this->assertSame(
            0,
            $this->product->getMinNumberInCart()
        );
    }

    /**
     * @test
     */
    public function setMinNumberInCartIfMinNumberIsEqualToMaxNumber()
    {
        $minNumber = 1;
        $maxNumber = 1;

        $this->product->setMaxNumberInCart($maxNumber);
        $this->product->setMinNumberInCart($minNumber);

        $this->assertEquals(
            $minNumber,
            $this->product->getMinNumberInCart()
        );
    }

    /**
     * @test
     */
    public function setMinNumberInCartIfMinNumberIsLesserThanMax()
    {
        $minNumber = 1;
        $maxNumber = 2;

        $this->product->setMaxNumberInCart($maxNumber);
        $this->product->setMinNumberInCart($minNumber);

        $this->assertEquals(
            $minNumber,
            $this->product->getMinNumberInCart()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function throwsInvalidArgumentExceptionIfMinNumberIsGreaterThanMaxNumber()
    {
        $minNumber = 2;
        $maxNumber = 1;

        $this->product->setMaxNumberInCart($maxNumber);
        $this->product->setMinNumberInCart($minNumber);
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function throwsInvalidArgumentExceptionIfMinNumberIsNegativ()
    {
        $minNumber = -1;
        $maxNumber = 1;

        $this->product->setMaxNumberInCart($maxNumber);
        $this->product->setMinNumberInCart($minNumber);
    }

    /**
     * @test
     */
    public function getMaxNumberInCartReturnsInitialValueMaxNumber()
    {
        $this->assertSame(
            0,
            $this->product->getMaxNumberInCart()
        );
    }

    /**
     * @test
     */
    public function setMaxNumberInCartIfMaxNumberIsEqualToMinNumber()
    {
        $minNumber = 1;
        $maxNumber = 1;

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertEquals(
            $maxNumber,
            $this->product->getMaxNumberInCart()
        );
    }

    /**
     * @test
     */
    public function setMaxNumberInCartIfMaxNumerIsGreaterThanMinNumber()
    {
        $minNumber = 1;
        $maxNumber = 2;

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertEquals(
            $maxNumber,
            $this->product->getMaxNumberInCart()
        );
    }

    /**
     * @test
     * @expectedException \InvalidArgumentException
     */
    public function throwsInvalidArgumentExceptionIfMaxNumberIsLesserThanMinNUmber()
    {
        $minNumber = 2;
        $maxNumber = 1;

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);
    }

    /**
     * @test
     */
    public function getQuantityIsLeavingRangeReturnsZeroIfQuantityIsInRange()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 7;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            0,
            $this->product->getQuantityIsLeavingRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsLeavingRangeReturnsZeroIfQuantityIsEqualToMinimum()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 5;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            0,
            $this->product->getQuantityIsLeavingRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsLeavingRangeReturnsZeroIfQuantityIsEqualToMaximum()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 10;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            0,
            $this->product->getQuantityIsLeavingRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsLeavingRangeReturnsMinusOneIfQuantityIsLessThanMinimum()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            -1,
            $this->product->getQuantityIsLeavingRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsLeavingRangeReturnsOneIfQuantityIsGreaterThanMaximum()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 11;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            1,
            $this->product->getQuantityIsLeavingRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsInRangeReturnsTrueIfQuantityIsInRange()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 7;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertTrue(
            $this->product->getQuantityIsInRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsInRangeReturnsTrueIfQuantityIsEqualToMinimum()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 5;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertTrue(
            $this->product->getQuantityIsInRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsInRangeReturnsTrueIfQuantityIsEqualToMaximum()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 10;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertTrue(
            $this->product->getQuantityIsInRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsInRangeReturnsFalseIfQuantityIsLessThanMinimum()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertFalse(
            $this->product->getQuantityIsInRange()
        );
    }

    /**
     * @test
     */
    public function getQuantityIsInRangeReturnsFalseIfQuantityIsGreaterThanMaximum()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 11;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertFalse(
            $this->product->getQuantityIsInRange()
        );
    }

    /**
     * @test
     */
    public function getGrossReturnsZeroIfNumberIsOutOfRange()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            0.0,
            $this->product->getGross()
        );

        $quantity = 11;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            0.0,
            $this->product->getGross()
        );
    }

    /**
     * @test
     */
    public function getNetReturnsZeroIfNumberIsOutOfRange()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            0.0,
            $this->product->getNet()
        );

        $quantity = 11;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            0.0,
            $this->product->getNet()
        );
    }

    /**
     * @test
     */
    public function getTaxReturnsZeroIfNumberIsOutOfRange()
    {
        $minNumber = 5;
        $maxNumber = 10;
        $quantity = 4;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            0.0,
            $this->product->getTax()
        );

        $quantity = 11;

        $this->product = new \Extcode\Cart\Domain\Model\Cart\Product(
            $this->productType,
            $this->productId,
            $this->tableId,
            $this->contentId,
            $this->sku,
            $this->title,
            $this->price,
            $this->taxClass,
            $quantity
        );

        $this->product->setMinNumberInCart($minNumber);
        $this->product->setMaxNumberInCart($maxNumber);

        $this->assertSame(
            0.0,
            $this->product->getTax()
        );
    }

    /**
     * @test
     */
    public function getIsVirtualProductInitiallyReturnsFalse()
    {
        $this->assertFalse(
            $this->product->getIsVirtualProduct()
        );
    }

    /**
     * @test
     */
    public function setIsVirtualProductSetsIsVirtualProduct()
    {
        $this->product->setIsVirtualProduct(true);

        $this->assertTrue(
            $this->product->getIsVirtualProduct()
        );
    }
}
