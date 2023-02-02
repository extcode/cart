<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\CartCouponFix;
use Extcode\Cart\Domain\Model\Cart\Product;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CartTest extends UnitTestCase
{
    protected Cart $grossCart;

    protected Cart $netCart;

    protected TaxClass $normalTaxClass;

    protected TaxClass $reducedTaxClass;

    protected TaxClass $freeTaxClass;

    protected array $taxClasses = [];

    public function setUp(): void
    {
        $this->normalTaxClass = new TaxClass(1, '19', 0.19, 'Normal');
        $this->reducedTaxClass = new TaxClass(2, '7%', 0.07, 'Reduced');
        $this->freeTaxClass = new TaxClass(3, '0%', 0.00, 'Free');

        $this->taxClasses = [
            1 => $this->normalTaxClass,
            2 => $this->reducedTaxClass,
            3 => $this->freeTaxClass,
        ];

        $this->grossCart = new Cart($this->taxClasses, false);
        $this->netCart = new Cart($this->taxClasses, true);

        parent::setUp();
    }

    public function tearDown(): void
    {
        unset($this->grossCart);
        unset($this->netCart);

        unset($this->taxClasses);

        unset($this->normalTaxClass);
        unset($this->reducedTaxClass);
        unset($this->freeTaxClass);

        parent::tearDown();
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->grossCart->getNet()
        );

        self::assertSame(
            0.0,
            $this->netCart->getNet()
        );
    }

    /**
     * @test
     */
    public function getSubtotalNetInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->grossCart->getSubtotalNet()
        );

        self::assertSame(
            0.0,
            $this->netCart->getSubtotalNet()
        );
    }

    /**
     * @test
     */
    public function getTotalNetInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->grossCart->getTotalNet()
        );

        self::assertSame(
            0.0,
            $this->netCart->getTotalNet()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->grossCart->getGross()
        );

        self::assertSame(
            0.0,
            $this->netCart->getGross()
        );
    }

    /**
     * @test
     */
    public function getTotalGrossInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->grossCart->getTotalGross()
        );

        self::assertSame(
            0.0,
            $this->netCart->getTotalGross()
        );
    }

    /**
     * @test
     */
    public function getTaxesInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->grossCart->getTaxes()
        );

        self::assertEmpty(
            $this->netCart->getTaxes()
        );
    }

    /**
     * @test
     */
    public function getCouponTaxesInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->grossCart->getCouponTaxes()
        );

        self::assertEmpty(
            $this->netCart->getCouponTaxes()
        );
    }

    /**
     * @test
     */
    public function getSubtotalTaxesInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->grossCart->getSubtotalTaxes()
        );

        self::assertEmpty(
            $this->netCart->getSubtotalTaxes()
        );
    }

    /**
     * @test
     */
    public function getTotalTaxesInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->grossCart->getTotalTaxes()
        );

        self::assertEmpty(
            $this->netCart->getTotalTaxes()
        );
    }

    /**
     * @test
     */
    public function getCountInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->grossCart->getCount()
        );
    }

    /**
     * @test
     */
    public function getCountPhysicalProductsInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->grossCart->getCountPhysicalProducts()
        );
    }

    /**
     * @test
     */
    public function getCountVirtualProductsInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->grossCart->getCountVirtualProducts()
        );
    }

    /**
     * @test
     */
    public function getProductsInitiallyReturnsEmptyArray(): void
    {
        self::assertCount(
            0,
            $this->grossCart->getProducts()
        );
    }

    /**
     * @test
     */
    public function getOrderNumberInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->grossCart->getOrderNumber()
        );
    }

    /**
     * @test
     */
    public function setInitiallyOrderNumberSetsOrderNumber(): void
    {
        $this->grossCart->setOrderNumber('ValidOrderNumber');

        self::assertSame(
            'ValidOrderNumber',
            $this->grossCart->getOrderNumber()
        );
    }

    /**
     * @test
     */
    public function resetSameOrderNumberSetsOrderNumber(): void
    {
        $this->grossCart->setOrderNumber('ValidOrderNumber');

        $this->grossCart->setOrderNumber('ValidOrderNumber');

        self::assertSame(
            'ValidOrderNumber',
            $this->grossCart->getOrderNumber()
        );
    }

    /**
     * @test
     */
    public function resetDifferentOrderNumberThrowsException(): void
    {
        $this->grossCart->setOrderNumber('ValidOrderNumber');

        $this->expectException(
            'LogicException'
        );
        $this->expectExceptionMessage(
            'You can not redeclare the order number of your cart.'
        );
        $this->expectExceptionCode(
            1413969668
        );

        $this->grossCart->setOrderNumber('NotValidOrderNumber');
    }

    public function resetOrderNumberWithResetOrderNumberMethodSetsOrderNumberToEmptyString()
    {
        $this->grossCart->setOrderNumber('ValidOrderNumber');

        $this->grossCart->resetOrderNumber();

        self::assertSame(
            '',
            $this->grossCart->getOrderNumber()
        );
    }

    /**
     * @test
     */
    public function getInvoiceNumberInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->grossCart->getInvoiceNumber()
        );
    }

    /**
     * @test
     */
    public function setInitiallyInvoiceNumberSetsInvoiceNumber(): void
    {
        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        self::assertSame(
            'ValidInvoiceNumber',
            $this->grossCart->getInvoiceNumber()
        );
    }

    /**
     * @test
     */
    public function resetSameInvoiceNumberSetsInvoiceNumber(): void
    {
        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        self::assertSame(
            'ValidInvoiceNumber',
            $this->grossCart->getInvoiceNumber()
        );
    }

    /**
     * @test
     */
    public function resetDifferentInvoiceNumberThrowsException(): void
    {
        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        $this->expectException(
            'LogicException'
        );
        $this->expectExceptionMessage(
            'You can not redeclare the invoice number of your cart.',
        );
        $this->expectExceptionCode(
            1413969712
        );

        $this->grossCart->setInvoiceNumber('NotValidInvoiceNumber');
    }

    public function resetInvoiceNumberWithResetInvoiceNumberMethodSetsInvoiceNumberToEmptyString()
    {
        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        $this->grossCart->resetInvoiceNumber();

        self::assertSame(
            '',
            $this->grossCart->getInvoiceNumber()
        );
    }

    /**
     * @test
     */
    public function addFirstCartProductToCartChangeCountOfProducts(): void
    {
        $product = new Product(
            'simple',
            1,
            'SKU',
            'First Product',
            10.00,
            $this->normalTaxClass,
            1,
            false
        );

        $this->grossCart->addProduct($product);

        self::assertSame(
            1,
            $this->grossCart->getCount()
        );
    }

    /**
     * @test
     */
    public function addFirstPhysicalCartProductToCartChangeCountOfPhysicalProducts(): void
    {
        $product = new Product(
            'simple',
            1,
            'SKU',
            'First Product',
            10.00,
            $this->normalTaxClass,
            1,
            false
        );

        $this->grossCart->addProduct($product);

        self::assertSame(
            1,
            $this->grossCart->getCountPhysicalProducts()
        );

        self::assertSame(
            0,
            $this->grossCart->getCountVirtualProducts()
        );
    }

    /**
     * @test
     */
    public function addFirstVirtualCartProductToCartChangeCountOfVirtualProducts(): void
    {
        $product = new Product(
            'simple',
            1,
            'SKU',
            'First Product',
            10.00,
            $this->normalTaxClass,
            1,
            false
        );
        $product->setIsVirtualProduct(true);

        $this->grossCart->addProduct($product);

        self::assertSame(
            0,
            $this->grossCart->getCountPhysicalProducts()
        );

        self::assertSame(
            1,
            $this->grossCart->getCountVirtualProducts()
        );
    }

    // Change Net Of Cart

    /**
     * @test
     */
    public function addFirstGrossCartProductToGrossCartChangeNetOfCart(): void
    {
        $productPrice = 10.00;
        $grossProduct = new Product(
            'simple',
            1,
            'SKU',
            'First Gross Product',
            $productPrice,
            $this->normalTaxClass,
            1,
            false
        );

        $this->grossCart->addProduct($grossProduct);

        self::assertEquals(
            $productPrice / (1 + $this->normalTaxClass->getCalc()),
            $this->grossCart->getNet()
        );
    }

    /**
     * @test
     */
    public function addFirstNetCartProductToNetCartChangeNetOfCart(): void
    {
        $productPrice = 10.00;
        $netProduct = new Product(
            'simple',
            1,
            'SKU',
            'First Net Product',
            $productPrice,
            $this->normalTaxClass,
            1,
            true
        );

        $this->netCart->addProduct($netProduct);

        self::assertEquals(
            $productPrice,
            $this->netCart->getNet()
        );
    }

    /**
     * @test
     */
    public function addFirstGrossCartProductToNetCartChangeNetOfCart(): void
    {
        $productPrice = 10.00;
        $grossProduct = new Product(
            'simple',
            1,
            'SKU',
            'First Gross Product',
            $productPrice,
            $this->normalTaxClass,
            1,
            false
        );

        $this->netCart->addProduct($grossProduct);

        self::assertEquals(
            $productPrice / (1 + $this->normalTaxClass->getCalc()),
            $this->netCart->getNet()
        );
    }

    /**
     * @test
     */
    public function addFirstNetCartProductToGrossCartChangeNetOfCart(): void
    {
        $productPrice = 10.00;
        $netProduct = new Product(
            'simple',
            1,
            'SKU',
            'First Net Product',
            $productPrice,
            $this->normalTaxClass,
            1,
            true
        );

        $this->grossCart->addProduct($netProduct);

        self::assertEquals(
            $productPrice,
            $this->grossCart->getNet()
        );
    }

    // Change Gross Of Cart

    /**
     * @test
     */
    public function addFirstGrossCartProductToGrossCartChangeGrossOfCart(): void
    {
        $productPrice = 10.00;
        $grossProduct = new Product(
            'simple',
            1,
            'SKU',
            'First Gross Product',
            $productPrice,
            $this->normalTaxClass,
            1,
            false
        );

        $this->grossCart->addProduct($grossProduct);

        self::assertEquals(
            $productPrice,
            $this->grossCart->getGross()
        );
    }

    /**
     * @test
     */
    public function addFirstNetCartProductToNetCartChangeGrossOfCart(): void
    {
        $productPrice = 10.00;
        $netProduct = new Product(
            'simple',
            1,
            'SKU',
            'First Net Product',
            $productPrice,
            $this->normalTaxClass,
            1,
            true
        );

        $this->netCart->addProduct($netProduct);

        self::assertEqualsWithDelta(
            $productPrice * (1 + $this->normalTaxClass->getCalc()),
            $this->netCart->getGross(),
            0.000000000001
        );
    }

    /**
     * @test
     */
    public function addFirstGrossCartProductToNetCartChangeGrossOfCart(): void
    {
        $productPrice = 10.00;
        $grossProduct = new Product(
            'simple',
            1,
            'SKU',
            'First Gross Product',
            $productPrice,
            $this->normalTaxClass,
            1,
            false
        );

        $this->netCart->addProduct($grossProduct);

        self::assertEquals(
            $productPrice,
            $this->netCart->getGross()
        );
    }

    /**
     * @test
     */
    public function addFirstNetCartProductToGrossCartChangeGrossOfCart(): void
    {
        $productPrice = 10.00;
        $netProduct = new Product(
            'simple',
            1,
            'SKU',
            'First Net Product',
            $productPrice,
            $this->normalTaxClass,
            1,
            true
        );

        $this->grossCart->addProduct($netProduct);

        self::assertEqualsWithDelta(
            $productPrice * (1 + $this->normalTaxClass->getCalc()),
            $this->grossCart->getGross(),
            0.000000000001
        );
    }

    /**
     * @test
     */
    public function addFirstCartProductToCartChangeTaxArray(): void
    {
        $taxId = 1;
        $productPrice = 10.00;
        $product = new Product(
            'simple',
            1,
            'SKU',
            'First Product',
            $productPrice,
            $this->normalTaxClass,
            1,
            false
        );

        $this->grossCart->addProduct($product);

        $cartTaxes = $this->grossCart->getTaxes();

        self::assertEqualsWithDelta(
            $productPrice - ($productPrice / (1 + $this->normalTaxClass->getCalc())),
            $cartTaxes[$taxId],
            0.000000000001
        );
    }

    /**
     * @test
     */
    public function addSecondCartProductWithSameTaxClassToCartChangeTaxArray(): void
    {
        $firstCartProductPrice = 10.00;
        $firstCartProduct = new Product(
            'simple',
            1,
            'SKU 1',
            'First Product',
            $firstCartProductPrice,
            $this->normalTaxClass,
            1,
            false
        );
        $this->grossCart->addProduct($firstCartProduct);

        $secondCartProductPrice = 20.00;
        $secondCartProduct = new Product(
            'simple',
            2,
            'SKU 2',
            'Second Product',
            $secondCartProductPrice,
            $this->normalTaxClass,
            1,
            false
        );
        $this->grossCart->addProduct($secondCartProduct);

        $cartTaxes = $this->grossCart->getTaxes();

        self::assertEqualsWithDelta(
            ($firstCartProductPrice + $secondCartProductPrice) - (($firstCartProductPrice + $secondCartProductPrice) / (1 + $this->normalTaxClass->getCalc())),
            $cartTaxes[$this->normalTaxClass->getId()],
            0.000000000001
        );
    }

    /**
     * @test
     */
    public function addSecondCartProductWithDifferentTaxClassToCartChangeTaxArray(): void
    {
        $firstCartProductPrice = 10.00;
        $firstCartProduct = new Product(
            'simple',
            1,
            'SKU 1',
            'First Product',
            $firstCartProductPrice,
            $this->normalTaxClass,
            1,
            false
        );
        $this->grossCart->addProduct($firstCartProduct);

        $secondCartProductPrice = 20.00;
        $secondCartProduct = new Product(
            'simple',
            2,
            'SKU 2',
            'Second Product',
            $secondCartProductPrice,
            $this->reducedTaxClass,
            1,
            false
        );
        $this->grossCart->addProduct($secondCartProduct);

        $cartTaxes = $this->grossCart->getTaxes();

        self::assertEqualsWithDelta(
            $firstCartProductPrice - ($firstCartProductPrice / (1 + $this->normalTaxClass->getCalc())),
            $cartTaxes[$this->normalTaxClass->getId()],
            0.000000000001
        );
        self::assertEqualsWithDelta(
            $secondCartProductPrice - ($secondCartProductPrice / (1 + $this->reducedTaxClass->getCalc())),
            $cartTaxes[$this->reducedTaxClass->getId()],
            0.000000000001
        );
    }

    /**
     * @test
     */
    public function isOrderableOfEmptyCartReturnsFalse(): void
    {
        self::assertFalse(
            $this->grossCart->isOrderable()
        );
    }

    /**
     * @test
     */
    public function isOrderableOfCartReturnsTrueWhenProductNumberIsInRangeForAllProducts(): void
    {
        $taxClass = new TaxClass(1, '19', 0.19, 'normal');

        $product = $this->getMockBuilder(Product::class)
            ->onlyMethods(['getBestPrice', 'getId', 'isQuantityInRange'])
            ->setConstructorArgs(
                [
                    'Cart',
                    1,
                    'SKU',
                    'TITLE',
                    10.00,
                    $taxClass,
                    1,
                ]
            )->getMock();
        $product->method('getBestPrice')->willReturn(10.00);
        $product->method('getId')->willReturn('simple_1');
        $product->method('isQuantityInRange')->willReturn(true);

        $this->grossCart->addProduct($product);

        $product = $this->getMockBuilder(Product::class)
            ->onlyMethods(['getBestPrice', 'getId', 'isQuantityInRange'])
            ->setConstructorArgs(
                [
                    'Cart',
                    1,
                    'SKU',
                    'TITLE',
                    10.00,
                    $taxClass,
                    1,
                ]
            )->getMock();
        $product->method('getBestPrice')->willReturn(10.00);
        $product->method('getId')->willReturn('simple_2');
        $product->method('isQuantityInRange')->willReturn(true);

        $this->grossCart->addProduct($product);

        $product = $this->getMockBuilder(Product::class)
            ->onlyMethods(['getBestPrice', 'getId', 'isQuantityInRange'])
            ->setConstructorArgs(
                [
                    'Cart',
                    1,
                    'SKU',
                    'TITLE',
                    10.00,
                    $taxClass,
                    1,
                ]
            )->getMock();
        $product->method('getBestPrice')->willReturn(10.00);
        $product->method('getId')->willReturn('simple_3');
        $product->method('isQuantityInRange')->willReturn(true);

        $this->grossCart->addProduct($product);

        self::assertTrue(
            $this->grossCart->isOrderable()
        );
    }

    /**
     * @test
     */
    public function isOrderableOfCartReturnsFalseWhenProductNumberIsNotInRangeForOneProduct(): void
    {
        $taxClass = new TaxClass(1, '19', 0.19, 'normal');

        $product = $this->getMockBuilder(Product::class)
            ->onlyMethods(['getBestPrice', 'getId', 'isQuantityInRange'])
            ->setConstructorArgs(
                [
                    'Cart',
                    1,
                    'SKU',
                    'TITLE',
                    10.00,
                    $taxClass,
                    1,
                ]
            )->getMock();
        $product->method('getBestPrice')->willReturn(10.00);
        $product->method('getId')->willReturn('simple_1');
        $product->method('isQuantityInRange')->willReturn(true);

        $this->grossCart->addProduct($product);

        $product = $this->getMockBuilder(Product::class)
            ->onlyMethods(['getBestPrice', 'getId', 'isQuantityInRange'])
            ->setConstructorArgs(
                [
                    'Cart',
                    1,
                    'SKU',
                    'TITLE',
                    10.00,
                    $taxClass,
                    1,
                ]
            )->getMock();
        $product->method('getBestPrice')->willReturn(10.00);
        $product->method('getId')->willReturn('simple_2');
        $product->method('isQuantityInRange')->willReturn(false);

        $this->grossCart->addProduct($product);

        $product = $this->getMockBuilder(Product::class)
            ->onlyMethods(['getBestPrice', 'getId', 'isQuantityInRange'])
            ->setConstructorArgs(
                [
                    'Cart',
                    1,
                    'SKU',
                    'TITLE',
                    10.00,
                    $taxClass,
                    1,
                ]
            )->getMock();
        $product->method('getBestPrice')->willReturn(10.00);
        $product->method('getId')->willReturn('simple_3');
        $product->method('isQuantityInRange')->willReturn(true);

        $this->grossCart->addProduct($product);

        self::assertFalse(
            $this->grossCart->isOrderable()
        );
    }

    /**
     * @test
     */
    public function getCouponsInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->grossCart->getCoupons()
        );

        self::assertEmpty(
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function addCouponAddsNewCoupon(): void
    {
        $coupon = new CartCouponFix(
            'CouponTitle',
            'CouponCode',
            'CouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->grossCart->addCoupon($coupon);
        self::assertCount(
            1,
            $this->grossCart->getCoupons()
        );

        $this->netCart->addCoupon($coupon);
        self::assertCount(
            1,
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function addSameCouponReturnsReturnCodeOne(): void
    {
        $coupon = new CartCouponFix(
            'CouponTitle',
            'CouponCode',
            'CouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        self::assertSame(
            1,
            $this->grossCart->addCoupon($coupon)
        );

        self::assertSame(
            1,
            $this->netCart->addCoupon($coupon)
        );
    }

    /**
     * @test
     */
    public function addSameCouponDoesNotChangeCouponNumberInCart(): void
    {
        $coupon = new CartCouponFix(
            'CouponTitle',
            'CouponCode',
            'CouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->grossCart->addCoupon($coupon);
        $this->grossCart->addCoupon($coupon);
        self::assertCount(
            1,
            $this->grossCart->getCoupons()
        );

        $this->netCart->addCoupon($coupon);
        $this->netCart->addCoupon($coupon);
        self::assertCount(
            1,
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function addSameCouponReturnsErrorCodeMinusOne(): void
    {
        $coupon = new CartCouponFix(
            'CouponTitle',
            'CouponCode',
            'CouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->grossCart->addCoupon($coupon);
        self::assertSame(
            -1,
            $this->grossCart->addCoupon($coupon)
        );

        $this->netCart->addCoupon($coupon);
        self::assertSame(
            -1,
            $this->netCart->addCoupon($coupon)
        );
    }

    /**
     * @test
     */
    public function addSecondNotCombinableCouponDoesNotChangeCouponNumberInCart(): void
    {
        $firstCoupon = new CartCouponFix(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $secondCoupon = new CartCouponFix(
            'SecondCouponTitle',
            'SecondCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->grossCart->addCoupon($firstCoupon);
        $this->grossCart->addCoupon($secondCoupon);
        self::assertCount(
            1,
            $this->grossCart->getCoupons()
        );

        $this->netCart->addCoupon($firstCoupon);
        $this->netCart->addCoupon($secondCoupon);
        self::assertCount(
            1,
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function addSecondNotCombinableCouponReturnsReturnErrorCodeMinusTwo(): void
    {
        $firstCoupon = new CartCouponFix(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $secondCoupon = new CartCouponFix(
            'SecondCouponTitle',
            'SecondCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->grossCart->addCoupon($firstCoupon);
        self::assertSame(
            -2,
            $this->grossCart->addCoupon($secondCoupon)
        );

        $this->netCart->addCoupon($firstCoupon);
        self::assertSame(
            -2,
            $this->netCart->addCoupon($secondCoupon)
        );
    }

    /**
     * @test
     */
    public function addSecondCombinableCouponToNotCombinableCouponsDoesNotChangeCouponNumberInCart(): void
    {
        $firstCoupon = new CartCouponFix(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            false
        );

        $secondCoupon = new CartCouponFix(
            'SecondCouponTitle',
            'SecondCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            true
        );

        $this->grossCart->addCoupon($firstCoupon);
        $this->grossCart->addCoupon($secondCoupon);
        self::assertCount(
            1,
            $this->grossCart->getCoupons()
        );

        $this->netCart->addCoupon($firstCoupon);
        $this->netCart->addCoupon($secondCoupon);
        self::assertCount(
            1,
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function addSecondCombinableCouponToNotCombinableCouponsReturnsReturnErrorCodeMinusTwo(): void
    {
        $firstCoupon = new CartCouponFix(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            false
        );

        $secondCoupon = new CartCouponFix(
            'SecondCouponTitle',
            'SecondCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            true
        );

        $this->grossCart->addCoupon($firstCoupon);
        self::assertSame(
            -2,
            $this->grossCart->addCoupon($secondCoupon)
        );

        $this->netCart->addCoupon($firstCoupon);
        self::assertSame(
            -2,
            $this->netCart->addCoupon($secondCoupon)
        );
    }

    /**
     * @test
     */
    public function addSecondCombinableCouponAddsCoupon(): void
    {
        $firstCoupon = new CartCouponFix(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            true
        );

        $secondCoupon = new CartCouponFix(
            'SecondCouponTitle',
            'SecondCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            true
        );

        $this->grossCart->addCoupon($firstCoupon);
        $this->grossCart->addCoupon($secondCoupon);
        self::assertCount(
            2,
            $this->grossCart->getCoupons()
        );

        $this->netCart->addCoupon($firstCoupon);
        $this->netCart->addCoupon($secondCoupon);
        self::assertCount(
            2,
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function getCouponGrossInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->grossCart->getCouponGross()
        );

        self::assertSame(
            0.0,
            $this->netCart->getCouponGross()
        );
    }

    /**
     * @test
     */
    public function getCouponNetInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->grossCart->getCouponNet()
        );

        self::assertSame(
            0.0,
            $this->netCart->getCouponNet()
        );
    }

    /**
     * @test
     */
    public function getCouponGrossReturnsAllCouponsGrossSum(): void
    {
        $gross = 10.00;

        $firstCoupon = new CartCouponFix(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            false
        );

        $this->grossCart->addCoupon($firstCoupon);

        self::assertSame(
            $gross,
            $this->grossCart->getCouponGross()
        );

        $this->netCart->addCoupon($firstCoupon);

        self::assertSame(
            $gross,
            $this->netCart->getCouponGross()
        );
    }

    protected function addFirstProductToCarts(): void
    {
        $product = new Product(
            'simple',
            1,
            'SKU',
            'First Product',
            10.00,
            $this->normalTaxClass,
            1,
            false
        );

        $this->grossCart->addProduct($product);
        $this->netCart->addProduct($product);
    }

    /**
     * @test
     */
    public function getCouponGrossReturnsCouponsGrossSumOfCouponsWhenCartMinPriceWasReached(): void
    {
        $this->addFirstProductToCarts();

        $discount = 5.00;
        $cartMinPrice = 15.00;

        $firstCoupon = new CartCouponFix(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            $discount,
            $this->normalTaxClass,
            $cartMinPrice,
            false
        );

        $this->grossCart->addCoupon($firstCoupon);

        self::assertSame(
            0.0,
            $this->grossCart->getCouponGross()
        );

        $this->netCart->addCoupon($firstCoupon);

        self::assertSame(
            0.0,
            $this->netCart->getCouponGross()
        );
    }

    /**
     * @test
     */
    public function getCouponNetReturnsAllCouponsNetSum(): void
    {
        $discount = 10.00;
        $net = $discount / ($this->normalTaxClass->getCalc() + 1);

        $firstCoupon = new CartCouponFix(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0,
            true
        );

        $this->grossCart->addCoupon($firstCoupon);

        self::assertSame(
            $net,
            $this->grossCart->getCouponNet()
        );

        $this->netCart->addCoupon($firstCoupon);

        self::assertSame(
            $net,
            $this->netCart->getCouponNet()
        );
    }

    /**
     * @test
     */
    public function getCouponTaxReturnsAllCouponsTaxSum(): void
    {
        $gross = 10.00;
        $tax = $gross - ($gross / ($this->normalTaxClass->getCalc() + 1));

        $firstCoupon = new CartCouponFix(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            $gross,
            $this->normalTaxClass,
            0,
            true
        );

        $taxes = [];
        $taxes[$this->normalTaxClass->getId()] = $tax;

        $this->grossCart->addCoupon($firstCoupon);
        $result = $this->grossCart->getCouponTaxes();
        self::assertTrue(
            empty(array_diff_key($taxes, $result)) && empty(array_diff_key($result, $taxes))
        );
        self::assertTrue(
            empty(array_diff_assoc($taxes, $result)) && empty(array_diff_assoc($result, $taxes))
        );

        $this->netCart->addCoupon($firstCoupon);
        $result = $this->netCart->getCouponTaxes();
        self::assertTrue(
            empty(array_diff_key($taxes, $result)) && empty(array_diff_key($result, $taxes))
        );
        self::assertTrue(
            empty(array_diff_assoc($taxes, $result)) && empty(array_diff_assoc($result, $taxes))
        );
    }

    /**
     * @test
     */
    public function getSubtotalGrossInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->grossCart->getSubtotalGross()
        );

        self::assertSame(
            0.0,
            $this->netCart->getSubtotalGross()
        );
    }

    /**
     * @test
     */
    public function getSubtotalGrossReturnsSubtotalGross(): void
    {
        $price = 100.00;
        $couponGross = 10.00;

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getCouponGross', 'getCurrencyTranslation'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->method('getCouponGross')->willReturn($couponGross);
        $cart->method('getCurrencyTranslation')->willReturn(1.00);

        $product = new Product(
            'simple',
            1,
            'SKU',
            'First Product',
            100.00,
            $this->normalTaxClass,
            1,
            false
        );

        $cart->addProduct($product);

        self::assertSame(
            $price - $couponGross,
            $cart->getSubtotalGross()
        );
    }

    /**
     * @test
     */
    public function getSubtotalNetReturnsSubtotalNet(): void
    {
        $price = 100.00;
        $couponGross = 10.00;
        $couponNet = $couponGross / 1.19;

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getCouponNet', 'getCurrencyTranslation'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->method('getCouponNet')->willReturn($couponNet);
        $cart->method('getCurrencyTranslation')->willReturn(1.00);

        $product = new Product(
            'simple',
            1,
            'SKU',
            'First Product',
            $price,
            $this->normalTaxClass,
            1,
            false
        );
        $cart->addProduct($product);

        $subtotalNet = ($price / (1 + $this->normalTaxClass->getCalc())) - $couponNet;

        self::assertSame(
            $subtotalNet,
            $cart->getSubtotalNet()
        );
    }

    /**
     * @test
     */
    public function getCurrencyCodeInitiallyReturnsString(): void
    {
        self::assertSame(
            'EUR',
            $this->grossCart->getCurrencyCode()
        );

        self::assertSame(
            'EUR',
            $this->netCart->getCurrencyCode()
        );
    }

    /**
     * @test
     */
    public function constructorSetsCurrencyCode(): void
    {
        $this->grossCart = new Cart(
            $this->taxClasses,
            false,
            'USD',
            '$',
            1.5
        );

        self::assertSame(
            'USD',
            $this->grossCart->getCurrencyCode()
        );
    }

    /**
     * @test
     */
    public function setCurrencyCodeSetsCurrencyCode(): void
    {
        $this->grossCart->setCurrencyCode('USD');

        self::assertSame(
            'USD',
            $this->grossCart->getCurrencyCode()
        );

        $this->netCart->setCurrencyCode('USD');

        self::assertSame(
            'USD',
            $this->netCart->getCurrencyCode()
        );
    }

    /**
     * @test
     */
    public function getCurrencySignInitiallyReturnsString(): void
    {
        self::assertSame(
            '€',
            $this->grossCart->getCurrencySign()
        );

        self::assertSame(
            '€',
            $this->netCart->getCurrencySign()
        );
    }

    /**
     * @test
     */
    public function constructorSetsCurrencySign(): void
    {
        $this->grossCart = new Cart(
            $this->taxClasses,
            false,
            'USD',
            '$',
            1.5
        );

        self::assertSame(
            '$',
            $this->grossCart->getCurrencySign()
        );
    }

    /**
     * @test
     */
    public function setCurrencySignSetsCurrencySign(): void
    {
        $this->grossCart->setCurrencySign('$');

        self::assertSame(
            '$',
            $this->grossCart->getCurrencySign()
        );

        $this->netCart->setCurrencySign('$');

        self::assertSame(
            '$',
            $this->netCart->getCurrencySign()
        );
    }

    /**
     * @test
     */
    public function getCurrencyTranslationInitiallyReturnsFloat(): void
    {
        self::assertSame(
            1.0,
            $this->grossCart->getCurrencyTranslation()
        );

        self::assertSame(
            1.0,
            $this->netCart->getCurrencyTranslation()
        );
    }

    /**
     * @test
     */
    public function constructorSetsCurrencyTranslation(): void
    {
        $cart = new Cart(
            $this->taxClasses,
            false,
            'USD',
            '$',
            1.5
        );

        self::assertSame(
            1.5,
            $cart->getCurrencyTranslation()
        );
    }

    /**
     * @test
     */
    public function setCurrencyTranslationSetsCurrencyTranslation(): void
    {
        $this->grossCart->setCurrencyTranslation(1.5);

        self::assertSame(
            1.5,
            $this->grossCart->getCurrencyTranslation()
        );

        $this->netCart->setCurrencyTranslation(1.5);

        self::assertSame(
            1.5,
            $this->netCart->getCurrencyTranslation()
        );
    }

    /**
     * @test
     */
    public function translatePriceReturnsCorrectPrice(): void
    {
        self::assertSame(
            5.0,
            $this->grossCart->translatePrice(5.0)
        );

        self::assertSame(
            5.0,
            $this->netCart->translatePrice(5.0)
        );

        $this->grossCart->setCurrencyTranslation(2.0);
        $this->netCart->setCurrencyTranslation(2.0);

        self::assertSame(
            2.5,
            $this->grossCart->translatePrice(5.0)
        );

        self::assertSame(
            2.5,
            $this->netCart->translatePrice(5.0)
        );

        $this->grossCart->setCurrencyTranslation(0.5);
        $this->netCart->setCurrencyTranslation(0.5);

        self::assertSame(
            10.0,
            $this->grossCart->translatePrice(5.0)
        );

        self::assertSame(
            10.0,
            $this->netCart->translatePrice(5.0)
        );
    }
}
