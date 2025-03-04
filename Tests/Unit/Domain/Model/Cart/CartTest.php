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
use Extcode\Cart\Domain\Model\Cart\ProductFactory;
use Extcode\Cart\Domain\Model\Cart\ProductFactoryInterface;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use Extcode\Cart\Service\CurrencyTranslationService;
use Extcode\Cart\Service\CurrencyTranslationServiceInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Cart::class)]
class CartTest extends UnitTestCase
{
    private ProductFactoryInterface $productFactory;

    protected Cart $grossCart;

    protected Cart $netCart;

    protected TaxClass $normalTaxClass;

    protected TaxClass $reducedTaxClass;

    protected TaxClass $freeTaxClass;

    protected array $taxClasses = [];

    public function setUp(): void
    {
        parent::setUp();

        $this->productFactory = GeneralUtility::makeInstance(ProductFactory::class);

        $this->normalTaxClass = new TaxClass(1, '19 %', 0.19, 'Normal');
        $this->reducedTaxClass = new TaxClass(2, '7 %', 0.07, 'Reduced');
        $this->freeTaxClass = new TaxClass(3, '0 %', 0.00, 'Free');

        $this->taxClasses = [
            1 => $this->normalTaxClass,
            2 => $this->reducedTaxClass,
            3 => $this->freeTaxClass,
        ];

        $this->grossCart = $this->createCart(false);
        $this->netCart = $this->createCart(true);
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function getTaxesInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->grossCart->getTaxes()
        );

        self::assertEmpty(
            $this->netCart->getTaxes()
        );
    }

    #[Test]
    public function getCouponTaxesInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->grossCart->getCouponTaxes()
        );

        self::assertEmpty(
            $this->netCart->getCouponTaxes()
        );
    }

    #[Test]
    public function getSubtotalTaxesInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->grossCart->getSubtotalTaxes()
        );

        self::assertEmpty(
            $this->netCart->getSubtotalTaxes()
        );
    }

    #[Test]
    public function getTotalTaxesInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->grossCart->getTotalTaxes()
        );

        self::assertEmpty(
            $this->netCart->getTotalTaxes()
        );
    }

    #[Test]
    public function getCountInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->grossCart->getCount()
        );
    }

    #[Test]
    public function getCountPhysicalProductsInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->grossCart->getCountPhysicalProducts()
        );
    }

    #[Test]
    public function getCountVirtualProductsInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->grossCart->getCountVirtualProducts()
        );
    }

    #[Test]
    public function getProductsInitiallyReturnsEmptyArray(): void
    {
        self::assertCount(
            0,
            $this->grossCart->getProducts()
        );
    }

    #[Test]
    public function getOrderNumberInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->grossCart->getOrderNumber()
        );
    }

    #[Test]
    public function setInitiallyOrderNumberSetsOrderNumber(): void
    {
        $this->grossCart->setOrderNumber('ValidOrderNumber');

        self::assertSame(
            'ValidOrderNumber',
            $this->grossCart->getOrderNumber()
        );
    }

    #[Test]
    public function resetSameOrderNumberSetsOrderNumber(): void
    {
        $this->grossCart->setOrderNumber('ValidOrderNumber');

        $this->grossCart->setOrderNumber('ValidOrderNumber');

        self::assertSame(
            'ValidOrderNumber',
            $this->grossCart->getOrderNumber()
        );
    }

    #[Test]
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

    public function resetOrderNumberWithResetOrderNumberMethodSetsOrderNumberToEmptyString(): void
    {
        $this->grossCart->setOrderNumber('ValidOrderNumber');

        $this->grossCart->resetOrderNumber();

        self::assertSame(
            '',
            $this->grossCart->getOrderNumber()
        );
    }

    #[Test]
    public function getInvoiceNumberInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->grossCart->getInvoiceNumber()
        );
    }

    #[Test]
    public function setInitiallyInvoiceNumberSetsInvoiceNumber(): void
    {
        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        self::assertSame(
            'ValidInvoiceNumber',
            $this->grossCart->getInvoiceNumber()
        );
    }

    #[Test]
    public function resetSameInvoiceNumberSetsInvoiceNumber(): void
    {
        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        self::assertSame(
            'ValidInvoiceNumber',
            $this->grossCart->getInvoiceNumber()
        );
    }

    #[Test]
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

    public function resetInvoiceNumberWithResetInvoiceNumberMethodSetsInvoiceNumberToEmptyString(): void
    {
        $this->grossCart->setInvoiceNumber('ValidInvoiceNumber');

        $this->grossCart->resetInvoiceNumber();

        self::assertSame(
            '',
            $this->grossCart->getInvoiceNumber()
        );
    }

    #[Test]
    public function addFirstCartProductToCartChangeCountOfProducts(): void
    {
        $product = $this->productFactory->create(
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

    #[Test]
    public function addFirstPhysicalCartProductToCartChangeCountOfPhysicalProducts(): void
    {
        $product = $this->productFactory->create(
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

    #[Test]
    public function addFirstVirtualCartProductToCartChangeCountOfVirtualProducts(): void
    {
        $product = $this->productFactory->create(
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

    #[Test]
    public function addFirstGrossCartProductToGrossCartChangeNetOfCart(): void
    {
        $productPrice = 10.00;
        $grossProduct = $this->productFactory->create(
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

    #[Test]
    public function addFirstNetCartProductToNetCartChangeNetOfCart(): void
    {
        $productPrice = 10.00;
        $netProduct = $this->productFactory->create(
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

    #[Test]
    public function addFirstGrossCartProductToNetCartChangeNetOfCart(): void
    {
        $productPrice = 10.00;
        $grossProduct = $this->productFactory->create(
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

    #[Test]
    public function addFirstNetCartProductToGrossCartChangeNetOfCart(): void
    {
        $productPrice = 10.00;
        $netProduct = $this->productFactory->create(
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

    #[Test]
    public function addFirstGrossCartProductToGrossCartChangeGrossOfCart(): void
    {
        $productPrice = 10.00;
        $grossProduct = $this->productFactory->create(
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

    #[Test]
    public function addFirstNetCartProductToNetCartChangeGrossOfCart(): void
    {
        $productPrice = 10.00;
        $netProduct = $this->productFactory->create(
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

    #[Test]
    public function addFirstGrossCartProductToNetCartChangeGrossOfCart(): void
    {
        $productPrice = 10.00;
        $grossProduct = $this->productFactory->create(
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

    #[Test]
    public function addFirstNetCartProductToGrossCartChangeGrossOfCart(): void
    {
        $productPrice = 10.00;
        $netProduct = $this->productFactory->create(
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

    #[Test]
    public function addFirstCartProductToCartChangeTaxArray(): void
    {
        $taxId = 1;
        $productPrice = 10.00;
        $product = $this->productFactory->create(
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

    #[Test]
    public function addSecondCartProductWithSameTaxClassToCartChangeTaxArray(): void
    {
        $firstCartProductPrice = 10.00;
        $firstCartProduct = $this->productFactory->create(
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
        $secondCartProduct = $this->productFactory->create(
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

    #[Test]
    public function addSecondCartProductWithDifferentTaxClassToCartChangeTaxArray(): void
    {
        $firstCartProductPrice = 10.00;
        $firstCartProduct = $this->productFactory->create(
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
        $secondCartProduct = $this->productFactory->create(
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

    #[Test]
    public function isOrderableOfEmptyCartReturnsFalse(): void
    {
        self::assertFalse(
            $this->grossCart->isOrderable()
        );
    }

    #[Test]
    public function isOrderableOfCartReturnsTrueWhenProductNumberIsInRangeForAllProducts(): void
    {
        $taxClass = new TaxClass(1, '19 %', 0.19, 'normal');

        $product = $this->productFactory->create(
            'simple',
            1,
            'SKU',
            'TITLE',
            10.00,
            $taxClass,
            1,
        );
        self::assertTrue(
            $product->isQuantityInRange()
        );

        $this->grossCart->addProduct($product);

        $product = $this->productFactory->create(
            'simple',
            2,
            'SKU',
            'TITLE',
            10.00,
            $taxClass,
            1
        );
        self::assertTrue(
            $product->isQuantityInRange()
        );

        $this->grossCart->addProduct($product);

        $product = $this->productFactory->create(
            'simple',
            3,
            'SKU',
            'TITLE',
            10.00,
            $taxClass,
            1
        );
        self::assertTrue(
            $product->isQuantityInRange()
        );

        $this->grossCart->addProduct($product);

        self::assertTrue(
            $this->grossCart->isOrderable()
        );
    }

    #[Test]
    public function isOrderableOfCartReturnsFalseWhenProductNumberIsNotInRangeForOneProduct(): void
    {
        $taxClass = new TaxClass(1, '19 %', 0.19, 'normal');

        $product = $this->productFactory->create(
            'simple',
            1,
            'SKU',
            'TITLE',
            10.00,
            $taxClass,
            1,
        );
        self::assertTrue(
            $product->isQuantityInRange()
        );

        $this->grossCart->addProduct($product);

        $product = $this->productFactory->create(
            'simple',
            2,
            'SKU',
            'TITLE',
            10.00,
            $taxClass,
            10,
        );
        $product->setMaxNumberInCart(5);
        self::assertFalse(
            $product->isQuantityInRange()
        );

        $this->grossCart->addProduct($product);

        $product = $this->productFactory->create(
            'simple',
            3,
            'SKU',
            'TITLE',
            10.00,
            $taxClass,
            1,
        );
        self::assertTrue(
            $product->isQuantityInRange()
        );

        $this->grossCart->addProduct($product);

        self::assertFalse(
            $this->grossCart->isOrderable()
        );
    }

    #[Test]
    public function getCouponsInitiallyReturnsEmptyArray(): void
    {
        self::assertEmpty(
            $this->grossCart->getCoupons()
        );

        self::assertEmpty(
            $this->netCart->getCoupons()
        );
    }

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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
        $product = $this->productFactory->create(
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
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

    #[Test]
    public function getSubtotalGrossReturnsSubtotalGross(): void
    {
        $price = 100.00;
        $couponGross = 10.00;

        GeneralUtility::addInstance(
            CurrencyTranslationServiceInterface::class,
            new CurrencyTranslationService()
        );
        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getCouponGross', 'getCurrencyTranslation'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->method('getCouponGross')->willReturn($couponGross);
        $cart->method('getCurrencyTranslation')->willReturn(1.00);

        $product = $this->productFactory->create(
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

    #[Test]
    public function getSubtotalNetReturnsSubtotalNet(): void
    {
        $price = 100.00;
        $couponGross = 10.00;
        $couponNet = $couponGross / 1.19;

        GeneralUtility::addInstance(
            CurrencyTranslationServiceInterface::class,
            new CurrencyTranslationService()
        );
        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getCouponNet', 'getCurrencyTranslation'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->method('getCouponNet')->willReturn($couponNet);
        $cart->method('getCurrencyTranslation')->willReturn(1.00);

        $product = $this->productFactory->create(
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

    #[Test]
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

    #[Test]
    public function constructorSetsCurrencyCode(): void
    {
        GeneralUtility::addInstance(
            CurrencyTranslationServiceInterface::class,
            new CurrencyTranslationService()
        );
        $cart = new Cart(
            $this->taxClasses,
            false,
            'USD',
            '$',
            1.5
        );

        self::assertSame(
            'USD',
            $cart->getCurrencyCode()
        );
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function constructorSetsCurrencySign(): void
    {
        GeneralUtility::addInstance(
            CurrencyTranslationServiceInterface::class,
            new CurrencyTranslationService()
        );
        $cart = new Cart(
            $this->taxClasses,
            false,
            'USD',
            '$',
            1.5
        );

        self::assertSame(
            '$',
            $cart->getCurrencySign()
        );
    }

    #[Test]
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

    #[Test]
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

    #[Test]
    public function constructorSetsCurrencyTranslation(): void
    {
        GeneralUtility::addInstance(
            CurrencyTranslationServiceInterface::class,
            new CurrencyTranslationService()
        );
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

    #[Test]
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

    #[Test]
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

    private function createCart(bool $isNetCart): Cart
    {
        GeneralUtility::addInstance(
            CurrencyTranslationServiceInterface::class,
            new CurrencyTranslationService()
        );

        return new Cart($this->taxClasses, $isNetCart);
    }
}
