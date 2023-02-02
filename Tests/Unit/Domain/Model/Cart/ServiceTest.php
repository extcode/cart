<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Product;
use Extcode\Cart\Domain\Model\Cart\Service;
use Extcode\Cart\Domain\Model\Cart\TaxClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ServiceTest extends UnitTestCase
{
    protected int $id = 1;

    protected array $config = [];

    protected array $taxClasses = [];

    protected Service $service;

    protected TaxClass $normalTaxClass;

    protected TaxClass $reducedTaxClass;

    protected TaxClass $freeTaxClass;

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

        $this->config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => $this->normalTaxClass->getId(),
            'status' => 'open',
        ];

        $this->service = new Service(
            $this->id,
            $this->config
        );

        parent::setUp();
    }

    /**
     * @test
     */
    public function getServiceIdReturnsServiceIdSetByConstructor(): void
    {
        self::assertSame(
            $this->id,
            $this->service->getId()
        );
    }

    /**
     * @test
     */
    public function getServiceConfigReturnsServiceConfigSetByConstructor(): void
    {
        self::assertSame(
            $this->config,
            $this->service->getConfig()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithoutUntilAvailableConfigurationReturnsTrue(): void
    {
        self::assertTrue(
            $this->service->isAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithCartGrossInRangeReturnsTrue(): void
    {
        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open',
            'available' => [
                'from' => 20.00,
                'until' => 100.00,
            ],
        ];

        $service = new Service(
            $this->id,
            $config
        );

        $cart1 = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart1->method('getGross')->willReturn(20.00);
        $service->setCart($cart1);
        self::assertTrue(
            $service->isAvailable()
        );

        $cart2 = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart2->method('getGross')->willReturn(50.00);
        $service->setCart($cart2);
        self::assertTrue(
            $service->isAvailable()
        );

        $cart3 = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart3->method('getGross')->willReturn(100.00);
        $service->setCart($cart3);
        self::assertTrue(
            $service->isAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithCartGrossBelowRangeReturnsFalse(): void
    {
        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open',
            'available' => [
                'from' => 20.00,
                'until' => 100.00,
            ],
        ];

        $service = new Service(
            $this->id,
            $config
        );

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->method('getGross')->willReturn(19.99);

        $service->setCart($cart);

        self::assertFalse(
            $service->isAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithCartGrossAboveRangeReturnsFalse(): void
    {
        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open',
            'available' => [
                'from' => 20.00,
                'until' => 100.00,
            ],
        ];

        $service = new Service(
            $this->id,
            $config
        );

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->method('getGross')->willReturn(100.01);

        $service->setCart($cart);

        self::assertFalse(
            $service->isAvailable()
        );
    }

    /**
     * @test
     */
    public function isPresetInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->service->isPreset()
        );
    }

    /**
     * @test
     */
    public function setPresetSetsPreset(): void
    {
        $this->service->setPreset(true);
        self::assertTrue(
            $this->service->isPreset()
        );

        $this->service->setPreset(false);
        self::assertFalse(
            $this->service->isPreset()
        );
    }

    /**
     * @test
     */
    public function getFallbackIdInitiallyReturnsNull(): void
    {
        self::assertNull(
            $this->service->getFallbackId()
        );
    }

    /**
     * @test
     */
    public function getFallbackIdIReturnsConfiguredFallbackId(): void
    {
        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open',
            'fallBackId' => 3,
        ];

        $service = new Service(
            $this->id,
            $config
        );

        self::assertSame(
            3,
            $service->getFallbackId()
        );
    }

    /**
     * @test
     */
    public function isFreeInitiallyReturnsFalse(): void
    {
        self::assertNull(
            $this->service->getFallbackId()
        );
    }

    /**
     * @test
     */
    public function isFreeWithCartGrossInRangeReturnsTrue(): void
    {
        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open',
            'free' => [
                'from' => 20.00,
                'until' => 100.00,
            ],
        ];

        $service = new Service(
            $this->id,
            $config
        );

        $cart1 = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart1->method('getGross')->willReturn(20.00);
        $service->setCart($cart1);
        self::assertTrue(
            $service->isFree()
        );

        $cart2 = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart2->method('getGross')->willReturn(50.00);
        $service->setCart($cart2);
        self::assertTrue(
            $service->isFree()
        );

        $cart3 = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart3->method('getGross')->willReturn(100.00);
        $service->setCart($cart3);
        self::assertTrue(
            $service->isFree()
        );
    }

    /**
     * @test
     */
    public function isFreeWithCartGrossBelowRangeReturnsFalse(): void
    {
        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open',
            'free' => [
                'from' => 20.00,
                'until' => 100.00,
            ],
        ];

        $service = new Service(
            $this->id,
            $config
        );

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->method('getGross')->willReturn(19.99);

        $service->setCart($cart);

        self::assertFalse(
            $service->isFree()
        );
    }

    /**
     * @test
     */
    public function isFreeWithCartGrossAboveRangeReturnsFalse(): void
    {
        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open',
            'free' => [
                'from' => 20.00,
                'until' => 100.00,
            ],
        ];

        $service = new Service(
            $this->id,
            $config
        );

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->method('getGross')->willReturn(100.01);

        $service->setCart($cart);

        self::assertFalse(
            $service->isFree()
        );
    }
    /**
     * @test
     */
    public function taxClassIdsGreaterZeroReturnsTaxClass(): void
    {
        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->method('getGross')->willReturn(20.00);

        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => $this->normalTaxClass->getId(),
            'status' => 'open',
        ];
        $service = new Service(
            $this->id,
            $config
        );
        $service->setCart($cart);
        self::assertSame(
            $this->normalTaxClass,
            $service->getTaxClass()
        );

        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => $this->reducedTaxClass->getId(),
            'status' => 'open',
        ];
        $service = new Service(
            $this->id,
            $config
        );
        $service->setCart($cart);
        self::assertSame(
            $this->reducedTaxClass,
            $service->getTaxClass()
        );
    }

    /**
     * @test
     */
    public function forTaxClassIdMinusOneTheHighestUsedTaxRateWillBeUsed(): void
    {
        $config = [
            'title' => 'Standard',
            'extra' => '0.00',
            'taxClassId' => '-1',
            'status' => 'open',
        ];

        $service = new Service(
            $this->id,
            $config
        );

        $cart = $this->getMockBuilder(Cart::class)
            ->onlyMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->method('getGross')->willReturn(100.00);

        $firstCartProductPrice = 10.00;
        $firstCartProduct = new Product(
            'simple',
            1,
            'SKU 1',
            'First Product',
            $firstCartProductPrice,
            $this->reducedTaxClass,
            1,
            false
        );
        $cart->addProduct($firstCartProduct);
        $service->setCart($cart);

        self::assertSame(
            $this->reducedTaxClass->getId(),
            $service->getTaxClass()->getId()
        );

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
        $cart->addProduct($secondCartProduct);
        $service->setCart($cart);

        self::assertSame(
            $this->normalTaxClass->getId(),
            $service->getTaxClass()->getId()
        );
    }

    /**
     * @test
     */
    public function forTaxClassIdMinusTwoReturnsPseudoTaxClassWithIdMinusTwo(): void
    {
        $config = [
            'title' => 'Standard',
            'extra' => '0.00',
            'taxClassId' => '-2',
            'status' => 'open',
        ];

        $service = new Service(
            $this->id,
            $config
        );

        self::assertSame(
            -2,
            $service->getTaxClass()->getId()
        );
    }
}
