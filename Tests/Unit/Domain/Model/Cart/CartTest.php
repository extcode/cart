<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;

class CartTest extends UnitTestCase
{

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $grossCart = null;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $netCart = null;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $normalTaxClass = null;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $reducedTaxClass = null;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $freeTaxClass = null;

    /**
     * @var array
     */
    protected $taxClasses = [];

    /**
     *
     */
    public function setUp()
    {
        $this->normalTaxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'Normal');
        $this->reducedTaxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(2, '7%', 0.07, 'Reduced');
        $this->freeTaxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(3, '0%', 0.00, 'Free');

        $this->taxClasses = [
            1 => $this->normalTaxClass,
            2 => $this->reducedTaxClass,
            3 => $this->freeTaxClass
        ];

        $this->grossCart = new \Extcode\Cart\Domain\Model\Cart\Cart($this->taxClasses, false);
        $this->netCart = new \Extcode\Cart\Domain\Model\Cart\Cart($this->taxClasses, true);
    }

    /**
     *
     */
    public function tearDown()
    {
        unset($this->grossCart);
        unset($this->netCart);

        unset($this->taxClasses);

        unset($this->normalTaxClass);
        unset($this->reducedTaxClass);
        unset($this->freeTaxClass);
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->grossCart->getNet()
        );

        $this->assertSame(
            0.0,
            $this->netCart->getNet()
        );
    }

    /**
     * @test
     */
    public function getSubtotalNetInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->grossCart->getSubtotalNet()
        );

        $this->assertSame(
            0.0,
            $this->netCart->getSubtotalNet()
        );
    }

    /**
     * @test
     */
    public function getTotalNetInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->grossCart->getTotalNet()
        );

        $this->assertSame(
            0.0,
            $this->netCart->getTotalNet()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->grossCart->getGross()
        );

        $this->assertSame(
            0.0,
            $this->netCart->getGross()
        );
    }

    /**
     * @test
     */
    public function getTotalGrossInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->grossCart->getTotalGross()
        );

        $this->assertSame(
            0.0,
            $this->netCart->getTotalGross()
        );
    }

    /**
     * @test
     */
    public function getTaxesInitiallyReturnsEmptyArray()
    {
        $this->assertEmpty(
            $this->grossCart->getTaxes()
        );

        $this->assertEmpty(
            $this->netCart->getTaxes()
        );
    }

    /**
     * @test
     */
    public function getCouponTaxesInitiallyReturnsEmptyArray()
    {
        $this->assertEmpty(
            $this->grossCart->getCouponTaxes()
        );

        $this->assertEmpty(
            $this->netCart->getCouponTaxes()
        );
    }

    /**
     * @test
     */
    public function getSubtotalTaxesInitiallyReturnsEmptyArray()
    {
        $this->assertEmpty(
            $this->grossCart->getSubtotalTaxes()
        );

        $this->assertEmpty(
            $this->netCart->getSubtotalTaxes()
        );
    }

    /**
     * @test
     */
    public function getTotalTaxesInitiallyReturnsEmptyArray()
    {
        $this->assertEmpty(
            $this->grossCart->getTotalTaxes()
        );

        $this->assertEmpty(
            $this->netCart->getTotalTaxes()
        );
    }

    /**
     * @test
     */
    public function getCountInitiallyReturnsZero()
    {
        $this->assertSame(
            0,
            $this->grossCart->getCount()
        );
    }

    /**
     * @test
     */
    public function getCountPhysicalProductsInitiallyReturnsZero()
    {
        $this->assertSame(
            0,
            $this->grossCart->getCountPhysicalProducts()
        );
    }

    /**
     * @test
     */
    public function getCountVirtualProductsInitiallyReturnsZero()
    {
        $this->assertSame(
            0,
            $this->grossCart->getCountVirtualProducts()
        );
    }

    /**
     * @test
     */
    public function getProductsInitiallyReturnsEmptyArray()
    {
        $this->assertCount(
            0,
            $this->grossCart->getProducts()
        );
    }

    /**
     * @test
     */
    public function setInitiallyOrderNumberSetsOrderNumber()
    {
        $this->grossCart->setOrderNumber('ValidOrderNumber');

        $this->assertSame(
            'ValidOrderNumber',
            $this->grossCart->getOrderNumber()
        );
    }

    /**
     * @test
     */
    public function resetSameOrderNumberSetsOrderNumber()
    {
        $this->grossCart->setOrderNumber('ValidOrderNumber');

        $this->grossCart->setOrderNumber('ValidOrderNumber');

        $this->assertSame(
            'ValidOrderNumber',
            $this->grossCart->getOrderNumber()
        );
    }

    /**
     * @test
     */
    public function resetDifferentOrderNumberThrowsException()
    {
        $this->grossCart->setOrderNumber('ValidOrderNumber');

        $this->expectException(
            'LogicException',
            'You can not redeclare the order number of your cart.',
            1413969668
        );

        $this->grossCart->setOrderNumber('NotValidOrderNumber');
    }

    /**
     * @test
     */
    public function setInitiallyInvoiceNumberSetsInvoiceNumber()
    {
        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        $this->assertSame(
            'ValidInvoiceNumber',
            $this->grossCart->getInvoiceNumber()
        );
    }

    /**
     * @test
     */
    public function resetSameInvoiceNumberSetsInvoiceNumber()
    {
        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        $this->assertSame(
            'ValidInvoiceNumber',
            $this->grossCart->getInvoiceNumber()
        );
    }

    /**
     * @test
     */
    public function resetDifferentInvoiceNumberThrowsException()
    {
        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        $this->expectException(
            'LogicException',
            'You can not redeclare the invoice number of your cart.',
            1413969712
        );

        $this->grossCart->setInvoiceNumber('NotValidInvoiceNumber');
    }

    /**
     * @test
     */
    public function addFirstCartProductToCartChangeCountOfProducts()
    {
        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            1,
            $this->grossCart->getCount()
        );
    }

    /**
     * @test
     */
    public function addFirstPhysicalCartProductToCartChangeCountOfPhysicalProducts()
    {
        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            1,
            $this->grossCart->getCountPhysicalProducts()
        );

        $this->assertSame(
            0,
            $this->grossCart->getCountVirtualProducts()
        );
    }

    /**
     * @test
     */
    public function addFirstVirtualCartProductToCartChangeCountOfVirtualProducts()
    {
        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            0,
            $this->grossCart->getCountPhysicalProducts()
        );

        $this->assertSame(
            1,
            $this->grossCart->getCountVirtualProducts()
        );
    }

    // Change Net Of Cart

    /**
     * @test
     */
    public function addFirstGrossCartProductToGrossCartChangeNetOfCart()
    {
        $productPrice = 10.00;
        $grossProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $productPrice / (1 + $this->normalTaxClass->getCalc()),
            $this->grossCart->getNet()
        );
    }

    /**
     * @test
     */
    public function addFirstNetCartProductToNetCartChangeNetOfCart()
    {
        $productPrice = 10.00;
        $netProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $productPrice,
            $this->netCart->getNet()
        );
    }

    /**
     * @test
     */
    public function addFirstGrossCartProductToNetCartChangeNetOfCart()
    {
        $productPrice = 10.00;
        $grossProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $productPrice / (1 + $this->normalTaxClass->getCalc()),
            $this->netCart->getNet()
        );
    }

    /**
     * @test
     */
    public function addFirstNetCartProductToGrossCartChangeNetOfCart()
    {
        $productPrice = 10.00;
        $netProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $productPrice,
            $this->grossCart->getNet()
        );
    }

    // Change Gross Of Cart

    /**
     * @test
     */
    public function addFirstGrossCartProductToGrossCartChangeGrossOfCart()
    {
        $productPrice = 10.00;
        $grossProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $productPrice,
            $this->grossCart->getGross()
        );
    }

    /**
     * @test
     */
    public function addFirstNetCartProductToNetCartChangeGrossOfCart()
    {
        $productPrice = 10.00;
        $netProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $productPrice * (1 + $this->normalTaxClass->getCalc()),
            $this->netCart->getGross()
        );
    }

    /**
     * @test
     */
    public function addFirstGrossCartProductToNetCartChangeGrossOfCart()
    {
        $productPrice = 10.00;
        $grossProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $productPrice,
            $this->netCart->getGross()
        );
    }

    /**
     * @test
     */
    public function addFirstNetCartProductToGrossCartChangeGrossOfCart()
    {
        $productPrice = 10.00;
        $netProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $productPrice * (1 + $this->normalTaxClass->getCalc()),
            $this->grossCart->getGross()
        );
    }

    /**
     * @test
     */
    public function addFirstCartProductToCartChangeTaxArray()
    {
        $taxId = 1;
        $productPrice = 10.00;
        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $productPrice - ($productPrice / (1 + $this->normalTaxClass->getCalc())),
            $cartTaxes[$taxId]
        );
    }

    /**
     * @test
     */
    public function addSecondCartProductWithSameTaxClassToCartChangeTaxArray()
    {
        $firstCartProductPrice = 10.00;
        $firstCartProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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
        $secondCartProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            ($firstCartProductPrice + $secondCartProductPrice) - (($firstCartProductPrice + $secondCartProductPrice) / (1 + $this->normalTaxClass->getCalc())),
            $cartTaxes[$this->normalTaxClass->getId()]
        );
    }

    /**
     * @test
     */
    public function addSecondCartProductWithDifferentTaxClassToCartChangeTaxArray()
    {
        $firstCartProductPrice = 10.00;
        $firstCartProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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
        $secondCartProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $firstCartProductPrice - ($firstCartProductPrice / (1 + $this->normalTaxClass->getCalc())),
            $cartTaxes[$this->normalTaxClass->getId()]
        );
        $this->assertSame(
            $secondCartProductPrice - ($secondCartProductPrice / (1 + $this->reducedTaxClass->getCalc())),
            $cartTaxes[$this->reducedTaxClass->getId()]
        );
    }

    /**
     * @test
     */
    public function isOrderableOfEmptyCartReturnsFalse()
    {
        $this->assertFalse(
            $this->grossCart->getIsOrderable()
        );
    }

    /**
     * @test
     */
    public function isOrderableOfCartReturnsTrueWhenProductNumberIsInRangeForAllProducts()
    {
        $taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $product = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Product::class)
            ->setMethods(['getBestPrice', 'getId', 'getQuantityIsInRange'])
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
        $product->expects($this->any())->method('getBestPrice')->will($this->returnValue(10.00));
        $product->expects($this->any())->method('getId')->will($this->returnValue(1));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(true));

        $this->grossCart->addProduct($product);

        $product = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Product::class)
            ->setMethods(['getBestPrice', 'getId', 'getQuantityIsInRange'])
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
        $product->expects($this->any())->method('getBestPrice')->will($this->returnValue(10.00));
        $product->expects($this->any())->method('getId')->will($this->returnValue(2));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(true));

        $this->grossCart->addProduct($product);

        $product = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Product::class)
            ->setMethods(['getBestPrice', 'getId', 'getQuantityIsInRange'])
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
        $product->expects($this->any())->method('getBestPrice')->will($this->returnValue(10.00));
        $product->expects($this->any())->method('getId')->will($this->returnValue(3));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(true));

        $this->grossCart->addProduct($product);

        $this->assertTrue(
            $this->grossCart->getIsOrderable()
        );
    }

    /**
     * @test
     */
    public function isOrderableOfCartReturnsFalseWhenProductNumberIsNotInRangeForOneProduct()
    {
        $taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $product = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Product::class)
            ->setMethods(['getBestPrice', 'getId', 'getQuantityIsInRange'])
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
        $product->expects($this->any())->method('getBestPrice')->will($this->returnValue(10.00));
        $product->expects($this->any())->method('getId')->will($this->returnValue(1));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(true));

        $this->grossCart->addProduct($product);

        $product = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Product::class)
            ->setMethods(['getBestPrice', 'getId', 'getQuantityIsInRange'])
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
        $product->expects($this->any())->method('getBestPrice')->will($this->returnValue(10.00));
        $product->expects($this->any())->method('getId')->will($this->returnValue(2));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(false));

        $this->grossCart->addProduct($product);

        $product = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Product::class)
            ->setMethods(['getBestPrice', 'getId', 'getQuantityIsInRange'])
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
        $product->expects($this->any())->method('getBestPrice')->will($this->returnValue(10.00));
        $product->expects($this->any())->method('getId')->will($this->returnValue(3));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(true));

        $this->grossCart->addProduct($product);

        $this->assertFalse(
            $this->grossCart->getIsOrderable()
        );
    }

    /**
     * @test
     */
    public function getCouponsInitiallyReturnsEmptyArray()
    {
        $this->assertEmpty(
            $this->grossCart->getCoupons()
        );

        $this->assertEmpty(
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function addCouponAddsNewCoupon()
    {
        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'CouponTitle',
            'CouponCode',
            'CouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->grossCart->addCoupon($coupon);
        $this->assertCount(
            1,
            $this->grossCart->getCoupons()
        );

        $this->netCart->addCoupon($coupon);
        $this->assertCount(
            1,
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function addSameCouponReturnsReturnCodeOne()
    {
        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'CouponTitle',
            'CouponCode',
            'CouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->assertSame(
            1,
            $this->grossCart->addCoupon($coupon)
        );

        $this->assertSame(
            1,
            $this->netCart->addCoupon($coupon)
        );
    }

    /**
     * @test
     */
    public function addSameCouponDoesNotChangeCouponNumberInCart()
    {
        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'CouponTitle',
            'CouponCode',
            'CouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->grossCart->addCoupon($coupon);
        $this->grossCart->addCoupon($coupon);
        $this->assertCount(
            1,
            $this->grossCart->getCoupons()
        );

        $this->netCart->addCoupon($coupon);
        $this->netCart->addCoupon($coupon);
        $this->assertCount(
            1,
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function addSameCouponReturnsErrorCodeMinusOne()
    {
        $coupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'CouponTitle',
            'CouponCode',
            'CouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->grossCart->addCoupon($coupon);
        $this->assertSame(
            -1,
            $this->grossCart->addCoupon($coupon)
        );

        $this->netCart->addCoupon($coupon);
        $this->assertSame(
            -1,
            $this->netCart->addCoupon($coupon)
        );
    }

    /**
     * @test
     */
    public function addSecondNotCombinableCouponDoesNotChangeCouponNumberInCart()
    {
        $firstCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $secondCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'SecondCouponTitle',
            'SecondCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->grossCart->addCoupon($firstCoupon);
        $this->grossCart->addCoupon($secondCoupon);
        $this->assertCount(
            1,
            $this->grossCart->getCoupons()
        );

        $this->netCart->addCoupon($firstCoupon);
        $this->netCart->addCoupon($secondCoupon);
        $this->assertCount(
            1,
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function addSecondNotCombinableCouponReturnsReturnErrorCodeMinusTwo()
    {
        $firstCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $secondCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'SecondCouponTitle',
            'SecondCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00
        );

        $this->grossCart->addCoupon($firstCoupon);
        $this->assertSame(
            -2,
            $this->grossCart->addCoupon($secondCoupon)
        );

        $this->netCart->addCoupon($firstCoupon);
        $this->assertSame(
            -2,
            $this->netCart->addCoupon($secondCoupon)
        );
    }

    /**
     * @test
     */
    public function addSecondCombinableCouponToNotCombinableCouponsDoesNotChangeCouponNumberInCart()
    {
        $firstCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            false
        );

        $secondCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
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
        $this->assertCount(
            1,
            $this->grossCart->getCoupons()
        );

        $this->netCart->addCoupon($firstCoupon);
        $this->netCart->addCoupon($secondCoupon);
        $this->assertCount(
            1,
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function addSecondCombinableCouponToNotCombinableCouponsReturnsReturnErrorCodeMinusTwo()
    {
        $firstCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            false
        );

        $secondCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'SecondCouponTitle',
            'SecondCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            true
        );

        $this->grossCart->addCoupon($firstCoupon);
        $this->assertSame(
            -2,
            $this->grossCart->addCoupon($secondCoupon)
        );

        $this->netCart->addCoupon($firstCoupon);
        $this->assertSame(
            -2,
            $this->netCart->addCoupon($secondCoupon)
        );
    }

    /**
     * @test
     */
    public function addSecondCombinableCouponAddsCoupon()
    {
        $firstCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            true
        );

        $secondCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
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
        $this->assertCount(
            2,
            $this->grossCart->getCoupons()
        );

        $this->netCart->addCoupon($firstCoupon);
        $this->netCart->addCoupon($secondCoupon);
        $this->assertCount(
            2,
            $this->netCart->getCoupons()
        );
    }

    /**
     * @test
     */
    public function getCouponGrossInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->grossCart->getCouponGross()
        );

        $this->assertSame(
            0.0,
            $this->netCart->getCouponGross()
        );
    }

    /**
     * @test
     */
    public function getCouponNetInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->grossCart->getCouponNet()
        );

        $this->assertSame(
            0.0,
            $this->netCart->getCouponNet()
        );
    }

    /**
     * @test
     */
    public function getCouponGrossReturnsAllCouponsGrossSum()
    {
        $gross = 10.00;

        $firstCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0.00,
            false
        );

        $this->grossCart->addCoupon($firstCoupon);

        $this->assertSame(
            $gross,
            $this->grossCart->getCouponGross()
        );

        $this->netCart->addCoupon($firstCoupon);

        $this->assertSame(
            $gross,
            $this->netCart->getCouponGross()
        );
    }

    protected function addFirstProductToCarts()
    {
        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
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
    public function getCouponGrossReturnsCouponsGrossSumOfCouponsWhenCartMinPriceWasReached()
    {
        $this->addFirstProductToCarts();

        $discount = 5.00;
        $cartMinPrice = 15.00;

        $firstCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            $discount,
            $this->normalTaxClass,
            $cartMinPrice,
            false
        );

        $this->grossCart->addCoupon($firstCoupon);

        $this->assertSame(
            0.0,
            $this->grossCart->getCouponGross()
        );

        $this->netCart->addCoupon($firstCoupon);

        $this->assertSame(
            0.0,
            $this->netCart->getCouponGross()
        );
    }

    /**
     * @test
     */
    public function getCouponNetReturnsAllCouponsNetSum()
    {
        $discount = 10.00;
        $net = $discount / ($this->normalTaxClass->getCalc() + 1);

        $firstCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            10.00,
            $this->normalTaxClass,
            0,
            true
        );

        $this->grossCart->addCoupon($firstCoupon);

        $this->assertSame(
            $net,
            $this->grossCart->getCouponNet()
        );

        $this->netCart->addCoupon($firstCoupon);

        $this->assertSame(
            $net,
            $this->netCart->getCouponNet()
        );
    }

    /**
     * @test
     */
    public function getCouponTaxReturnsAllCouponsTaxSum()
    {
        $gross = 10.00;
        $tax = $gross - ($gross / ($this->normalTaxClass->getCalc() + 1));

        $firstCoupon = new \Extcode\Cart\Domain\Model\Cart\CartCoupon(
            'FirstCouponTitle',
            'FirstCouponCode',
            'FirstCouponType',
            $gross,
            $this->normalTaxClass,
            0,
            true
        );

        $this->grossCart->addCoupon($firstCoupon);

        $taxes = [];
        $taxes[$this->normalTaxClass->getId()] += $tax;
        $this->assertArraySubset(
            $taxes,
            $this->grossCart->getCouponTaxes()
        );

        $this->netCart->addCoupon($firstCoupon);

        $this->assertArraySubset(
            $taxes,
            $this->netCart->getCouponTaxes()
        );
    }

    /**
     * @test
     */
    public function getSubtotalGrossInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->grossCart->getSubtotalGross()
        );

        $this->assertSame(
            0.0,
            $this->netCart->getSubtotalGross()
        );
    }

    /**
     * @test
     */
    public function getSubtotalGrossReturnsSubtotalGross()
    {
        $price = 100.00;
        $couponGross = 10.00;

        $cart = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getCouponGross', 'getCurrencyTranslation'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->expects($this->any())->method('getCouponGross')->will($this->returnValue($couponGross));
        $cart->expects($this->any())->method('getCurrencyTranslation')->will($this->returnValue(1.00));

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $price - $couponGross,
            $cart->getSubtotalGross()
        );
    }

    /**
     * @test
     */
    public function getSubtotalNetReturnsSubtotalNet()
    {
        $price = 100.00;
        $couponGross = 10.00;
        $couponNet = $couponGross / 1.19;

        $cart = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getCouponNet', 'getCurrencyTranslation'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->expects($this->any())->method('getCouponNet')->will($this->returnValue($couponNet));
        $cart->expects($this->any())->method('getCurrencyTranslation')->will($this->returnValue(1.00));

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
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

        $this->assertSame(
            $subtotalNet,
            $cart->getSubtotalNet()
        );
    }

    /**
     * @test
     */
    public function getCurrencyCodeInitiallyReturnsString()
    {
        $this->assertSame(
            'EUR',
            $this->grossCart->getCurrencyCode()
        );

        $this->assertSame(
            'EUR',
            $this->netCart->getCurrencyCode()
        );
    }

    /**
     * @test
     */
    public function constructorSetsCurrencyCode()
    {
        $this->cart = new \Extcode\Cart\Domain\Model\Cart\Cart(
            $this->taxClasses,
            false,
            'USD',
            '$',
            1.5
        );

        $this->assertSame(
            'USD',
            $this->cart->getCurrencyCode()
        );
    }

    /**
     * @test
     */
    public function setCurrencyCodeSetsCurrencyCode()
    {
        $this->grossCart->setCurrencyCode('USD');

        $this->assertSame(
            'USD',
            $this->grossCart->getCurrencyCode()
        );

        $this->netCart->setCurrencyCode('USD');

        $this->assertSame(
            'USD',
            $this->netCart->getCurrencyCode()
        );
    }

    /**
     * @test
     */
    public function getCurrencySignInitiallyReturnsString()
    {
        $this->assertSame(
            '€',
            $this->grossCart->getCurrencySign()
        );

        $this->assertSame(
            '€',
            $this->netCart->getCurrencySign()
        );
    }

    /**
     * @test
     */
    public function constructorSetsCurrencySign()
    {
        $this->cart = new \Extcode\Cart\Domain\Model\Cart\Cart(
            $this->taxClasses,
            false,
            'USD',
            '$',
            1.5
        );

        $this->assertSame(
            '$',
            $this->cart->getCurrencySign()
        );
    }

    /**
     * @test
     */
    public function setCurrencySignSetsCurrencySign()
    {
        $this->grossCart->setCurrencySign('$');

        $this->assertSame(
            '$',
            $this->grossCart->getCurrencySign()
        );

        $this->netCart->setCurrencySign('$');

        $this->assertSame(
            '$',
            $this->netCart->getCurrencySign()
        );
    }

    /**
     * @test
     */
    public function getCurrencyTranslationInitiallyReturnsString()
    {
        $this->assertSame(
            1.0,
            $this->grossCart->getCurrencyTranslation()
        );

        $this->assertSame(
            1.0,
            $this->netCart->getCurrencyTranslation()
        );
    }

    /**
     * @test
     */
    public function constructorSetsCurrencyTranslation()
    {
        $this->cart = new \Extcode\Cart\Domain\Model\Cart\Cart(
            $this->taxClasses,
            false,
            'USD',
            '$',
            1.5
        );

        $this->assertSame(
            1.5,
            $this->cart->getCurrencyTranslation()
        );
    }

    /**
     * @test
     */
    public function setCurrencyTranslationSetsCurrencyTranslation()
    {
        $this->grossCart->setCurrencyTranslation(1.5);

        $this->assertSame(
            1.5,
            $this->grossCart->getCurrencyTranslation()
        );

        $this->netCart->setCurrencyTranslation(1.5);

        $this->assertSame(
            1.5,
            $this->netCart->getCurrencyTranslation()
        );
    }

    /**
     * @test
     */
    public function translatePriceReturnsCorrectPrice()
    {
        $this->assertSame(
            5.0,
            $this->grossCart->translatePrice(5.0)
        );

        $this->assertSame(
            5.0,
            $this->netCart->translatePrice(5.0)
        );

        $this->grossCart->setCurrencyTranslation(2.0);
        $this->netCart->setCurrencyTranslation(2.0);

        $this->assertSame(
            2.5,
            $this->grossCart->translatePrice(5.0)
        );

        $this->assertSame(
            2.5,
            $this->netCart->translatePrice(5.0)
        );

        $this->grossCart->setCurrencyTranslation(0.5);
        $this->netCart->setCurrencyTranslation(0.5);

        $this->assertSame(
            10.0,
            $this->grossCart->translatePrice(5.0)
        );

        $this->assertSame(
            10.0,
            $this->netCart->translatePrice(5.0)
        );
    }
}
