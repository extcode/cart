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
 * Cart Test
 *
 * @package cart
 * @author Daniel Lorenz
 * @license http://www.gnu.org/licenses/lgpl.html
 *                     GNU Lesser General Public License, version 3 or later
 */
class CartTest extends \TYPO3\CMS\Core\Tests\UnitTestCase
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
    protected $taxClasses = array();

    /**
     *
     */
    public function setUp()
    {

        $this->normalTaxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'Normal');
        $this->reducedTaxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(2, '7%', 0.07, 'Reduced');
        $this->freeTaxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(3, '0%', 0.00, 'Free');

        $this->taxClasses = array(
            1 => $this->normalTaxClass,
            2 => $this->reducedTaxClass,
            3 => $this->freeTaxClass
        );

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
        unset($taxClasses);
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

        $this->setExpectedException(
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

        $this->setExpectedException(
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
            1,
            0,
            0,
            1,
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

    // Change Net Of Cart

    /**
     * @test
     */
    public function addFirstGrossCartProductToGrossCartChangeNetOfCart()
    {
        $productPrice = 10.00;
        $grossProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
            1,
            0,
            0,
            1,
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
            1,
            0,
            0,
            1,
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
            1,
            0,
            0,
            1,
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
            1,
            0,
            0,
            1,
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
            1,
            0,
            0,
            1,
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
            1,
            0,
            0,
            1,
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
            1,
            0,
            0,
            1,
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
            1,
            0,
            0,
            1,
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
            1,
            0,
            0,
            1,
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
            1,
            0,
            0,
            1001,
            'First Product',
            $firstCartProductPrice,
            $this->normalTaxClass,
            1,
            false
        );
        $this->grossCart->addProduct($firstCartProduct);

        $secondCartProductPrice = 20.00;
        $secondCartProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
            2,
            0,
            0,
            1002,
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
            1,
            0,
            0,
            1001,
            'First Product',
            $firstCartProductPrice,
            $this->normalTaxClass,
            1,
            false
        );
        $this->grossCart->addProduct($firstCartProduct);

        $secondCartProductPrice = 20.00;
        $secondCartProduct = new \Extcode\Cart\Domain\Model\Cart\Product(
            2,
            0,
            0,
            1002,
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

        $product = $this->getMock('Extcode\\Cart\\Domain\\Model\\Cart\\Product', array(), array(), '', false);
        $product->expects($this->any())->method('getId')->will($this->returnValue(1));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(true));
        $product->expects($this->any())->method('getTaxClass')->will($this->returnValue($taxClass));

        $this->grossCart->addProduct($product);

        $product = $this->getMock('Extcode\\Cart\\Domain\\Model\\Cart\\Product', array(), array(), '', false);
        $product->expects($this->any())->method('getId')->will($this->returnValue(2));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(true));
        $product->expects($this->any())->method('getTaxClass')->will($this->returnValue($taxClass));

        $this->grossCart->addProduct($product);

        $product = $this->getMock('Extcode\\Cart\\Domain\\Model\\Cart\\Product', array(), array(), '', false);
        $product->expects($this->any())->method('getId')->will($this->returnValue(3));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(true));
        $product->expects($this->any())->method('getTaxClass')->will($this->returnValue($taxClass));

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

        $product = $this->getMock('Extcode\\Cart\\Domain\\Model\\Cart\\Product', array(), array(), '', false);
        $product->expects($this->any())->method('getId')->will($this->returnValue(1));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(true));
        $product->expects($this->any())->method('getTaxClass')->will($this->returnValue($taxClass));

        $this->grossCart->addProduct($product);

        $product = $this->getMock('Extcode\\Cart\\Domain\\Model\\Cart\\Product', array(), array(), '', false);
        $product->expects($this->any())->method('getId')->will($this->returnValue(2));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(false));
        $product->expects($this->any())->method('getTaxClass')->will($this->returnValue($taxClass));

        $this->grossCart->addProduct($product);

        $product = $this->getMock('Extcode\\Cart\\Domain\\Model\\Cart\\Product', array(), array(), '', false);
        $product->expects($this->any())->method('getId')->will($this->returnValue(3));
        $product->expects($this->any())->method('getQuantityIsInRange')->will($this->returnValue(true));
        $product->expects($this->any())->method('getTaxClass')->will($this->returnValue($taxClass));

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
        $coupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array('getCode', 'getTitle', 'getDiscount', 'getTaxClassId'),
            array(),
            '',
            false
        );
        $coupon->expects($this->any())->method('getCode')->will($this->returnValue('couponCode'));
        $coupon->expects($this->any())->method('getTitle')->will($this->returnValue('couponTitle'));
        $coupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $coupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

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
        $coupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array('getCode', 'getTitle', 'getDiscount', 'getTaxClassId'),
            array(),
            '',
            false
        );
        $coupon->expects($this->any())->method('getCode')->will($this->returnValue('couponCode'));
        $coupon->expects($this->any())->method('getTitle')->will($this->returnValue('couponTitle'));
        $coupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $coupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

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
        $coupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array('getCode', 'getTitle', 'getDiscount', 'getTaxClassId'),
            array(),
            '',
            false
        );
        $coupon->expects($this->any())->method('getCode')->will($this->returnValue('couponCode'));
        $coupon->expects($this->any())->method('getTitle')->will($this->returnValue('couponTitle'));
        $coupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $coupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

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
        $coupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array('getCode', 'getTitle', 'getDiscount', 'getTaxClassId'),
            array(),
            '',
            false
        );
        $coupon->expects($this->any())->method('getCode')->will($this->returnValue('couponCode'));
        $coupon->expects($this->any())->method('getTitle')->will($this->returnValue('couponTitle'));
        $coupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $coupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

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
        $firstCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array('getCode', 'getTitle', 'getDiscount', 'getTaxClassId'),
            array(),
            '',
            false
        );
        $firstCoupon->expects($this->any())->method('getCode')->will($this->returnValue('firstCouponCode'));
        $firstCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('firstCouponTitle'));
        $firstCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $firstCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

        $secondCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array('getCode', 'getTitle', 'getDiscount', 'getTaxClassId'),
            array(),
            '',
            false
        );
        $secondCoupon->expects($this->any())->method('getCode')->will($this->returnValue('secondCouponCode'));
        $secondCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('secondCouponTitle'));
        $secondCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $secondCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

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
        $firstCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array('getCode', 'getTitle', 'getDiscount', 'getTaxClassId'),
            array(),
            '',
            false
        );
        $firstCoupon->expects($this->any())->method('getCode')->will($this->returnValue('firstCouponCode'));
        $firstCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('firstCouponTitle'));
        $firstCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $firstCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

        $secondCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array('getCode', 'getTitle', 'getDiscount', 'getTaxClassId'),
            array(),
            '',
            false
        );
        $secondCoupon->expects($this->any())->method('getCode')->will($this->returnValue('secondCouponCode'));
        $secondCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('secondCouponTitle'));
        $secondCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $secondCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

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
        $firstCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array('getCode', 'getTitle', 'getDiscount', 'getTaxClassId'),
            array(),
            '',
            false
        );
        $firstCoupon->expects($this->any())->method('getCode')->will($this->returnValue('firstCouponCode'));
        $firstCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('firstCouponTitle'));
        $firstCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $firstCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

        $secondCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array(),
            array(),
            '',
            false
        );
        $secondCoupon->expects($this->any())->method('getCode')->will($this->returnValue('secondCouponCode'));
        $secondCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('secondCouponTitle'));
        $secondCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $secondCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));
        $secondCoupon->expects($this->any())->method('getIsCombinable')->will($this->returnValue(true));

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
        $firstCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array('getCode', 'getTitle', 'getDiscount', 'getTaxClassId'),
            array(),
            '',
            false
        );
        $firstCoupon->expects($this->any())->method('getCode')->will($this->returnValue('firstCouponCode'));
        $firstCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('firstCouponTitle'));
        $firstCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $firstCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

        $secondCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array(),
            array(),
            '',
            false
        );
        $secondCoupon->expects($this->any())->method('getCode')->will($this->returnValue('secondCouponCode'));
        $secondCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('secondCouponTitle'));
        $secondCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $secondCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));
        $secondCoupon->expects($this->any())->method('getIsCombinable')->will($this->returnValue(true));

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
        $firstCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array(),
            array(),
            '',
            false
        );
        $firstCoupon->expects($this->any())->method('getCode')->will($this->returnValue('firstCouponCode'));
        $firstCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('firstCouponTitle'));
        $firstCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $firstCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));
        $firstCoupon->expects($this->any())->method('getIsCombinable')->will($this->returnValue(true));

        $secondCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array(),
            array(),
            '',
            false
        );
        $secondCoupon->expects($this->any())->method('getCode')->will($this->returnValue('secondCouponCode'));
        $secondCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('secondCouponTitle'));
        $secondCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue(10.0));
        $secondCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));
        $secondCoupon->expects($this->any())->method('getIsCombinable')->will($this->returnValue(true));

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

        $firstCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array(),
            array(),
            '',
            false
        );
        $firstCoupon->expects($this->any())->method('getCode')->will($this->returnValue('firstCouponCode'));
        $firstCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('firstCouponTitle'));
        $firstCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue($gross));
        $firstCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));
        $firstCoupon->expects($this->any())->method('getIsCombinable')->will($this->returnValue(true));

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
            1,
            0,
            0,
            1,
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

        $firstCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array(),
            array(),
            '',
            false
        );
        $firstCoupon->expects($this->any())->method('getCode')->will($this->returnValue('firstCouponCode'));
        $firstCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('firstCouponTitle'));
        $firstCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue($discount));
        $firstCoupon->expects($this->any())->method('getCartMinPrice')->will($this->returnValue($cartMinPrice));
        $firstCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));

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

        $firstCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array(),
            array(),
            '',
            false
        );
        $firstCoupon->expects($this->any())->method('getCode')->will($this->returnValue('firstCouponCode'));
        $firstCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('firstCouponTitle'));
        $firstCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue($discount));
        $firstCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));
        $firstCoupon->expects($this->any())->method('getIsCombinable')->will($this->returnValue(true));

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
        $net = $gross / 1.19;
        $tax = $gross - $net;

        $firstCoupon = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Product\\Coupon',
            array(),
            array(),
            '',
            false
        );
        $firstCoupon->expects($this->any())->method('getCode')->will($this->returnValue('firstCouponCode'));
        $firstCoupon->expects($this->any())->method('getTitle')->will($this->returnValue('firstCouponTitle'));
        $firstCoupon->expects($this->any())->method('getDiscount')->will($this->returnValue($gross));
        $firstCoupon->expects($this->any())->
            method('getTaxClassId')->
            will($this->returnValue($this->normalTaxClass->getId()));
        $firstCoupon->expects($this->any())->method('getIsCombinable')->will($this->returnValue(true));

        $this->grossCart->addCoupon($firstCoupon);

        $this->assertArraySubset(
            array($this->normalTaxClass->getId() => $tax),
            $this->grossCart->getCouponTaxes()
        );

        $this->netCart->addCoupon($firstCoupon);

        $this->assertArraySubset(
            array($this->normalTaxClass->getId() => $tax),
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

        $cart = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Cart\\Cart',
            array('getCouponGross'),
            array(),
            '',
            false
        );
        $cart->expects($this->any())->method('getCouponGross')->will($this->returnValue($couponGross));

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            1,
            0,
            0,
            1,
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

        $cart = $this->getMock(
            'Extcode\\Cart\\Domain\\Model\\Cart\\Cart',
            array('getCouponNet'),
            array(),
            '',
            false
        );
        $cart->expects($this->any())->method('getCouponNet')->will($this->returnValue($couponNet));

        $product = new \Extcode\Cart\Domain\Model\Cart\Product(
            1,
            0,
            0,
            1,
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

}
