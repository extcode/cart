<?php

declare(strict_types=1);

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
use Extcode\Cart\Service\CurrencyTranslationService;
use Extcode\Cart\Service\CurrencyTranslationServiceInterface;
use PHPUnit\Framework\Attributes\AllowMockObjectsWithoutExpectations;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\MockObject\MockObject;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[AllowMockObjectsWithoutExpectations]
#[CoversClass(Service::class)]
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
        $this->normalTaxClass = new TaxClass(1, '19 %', 0.19, 'Normal');
        $this->reducedTaxClass = new TaxClass(2, '7 %', 0.07, 'Reduced');
        $this->freeTaxClass = new TaxClass(3, '0 %', 0.00, 'Free');

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

    #[Test]
    public function getServiceIdReturnsServiceIdSetByConstructor(): void
    {
        self::assertSame(
            $this->id,
            $this->service->getId()
        );
    }

    #[Test]
    public function getServiceConfigReturnsServiceConfigSetByConstructor(): void
    {
        self::assertSame(
            $this->config,
            $this->service->getConfig()
        );
    }

    #[Test]
    public function isAvailableWithoutUntilAvailableConfigurationReturnsTrue(): void
    {
        self::assertTrue(
            $this->service->isAvailable()
        );
    }

    #[Test]
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

        $cart1 = $this->createCartMock();
        $cart1->method('getGross')->willReturn(20.00);
        $service->setCart($cart1);
        self::assertTrue(
            $service->isAvailable()
        );

        $cart2 = $this->createCartMock();
        $cart2->method('getGross')->willReturn(50.00);
        $service->setCart($cart2);
        self::assertTrue(
            $service->isAvailable()
        );

        $cart3 = $this->createCartMock();
        $cart3->method('getGross')->willReturn(100.00);
        $service->setCart($cart3);
        self::assertTrue(
            $service->isAvailable()
        );
    }

    #[Test]
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

        $cart = $this->createCartMock();
        $cart->method('getGross')->willReturn(19.99);

        $service->setCart($cart);

        self::assertFalse(
            $service->isAvailable()
        );
    }

    #[Test]
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

        $cart = $this->createCartMock();
        $cart->method('getGross')->willReturn(100.01);

        $service->setCart($cart);

        self::assertFalse(
            $service->isAvailable()
        );
    }

    #[Test]
    public function isPresetInitiallyReturnsFalse(): void
    {
        self::assertFalse(
            $this->service->isPreset()
        );
    }

    #[Test]
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

    #[Test]
    public function getFallbackIdInitiallyReturnsNull(): void
    {
        self::assertNull(
            $this->service->getFallbackId()
        );
    }

    #[Test]
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

    #[Test]
    public function isFreeInitiallyReturnsFalse(): void
    {
        self::assertNull(
            $this->service->getFallbackId()
        );
    }

    #[Test]
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

        $cart1 = $this->createCartMock();
        $cart1->method('getGross')->willReturn(20.00);
        $service->setCart($cart1);
        self::assertTrue(
            $service->isFree()
        );

        $cart2 = $this->createCartMock();
        $cart2->method('getGross')->willReturn(50.00);
        $service->setCart($cart2);
        self::assertTrue(
            $service->isFree()
        );

        $cart3 = $this->createCartMock();
        $cart3->method('getGross')->willReturn(100.00);
        $service->setCart($cart3);
        self::assertTrue(
            $service->isFree()
        );
    }

    #[Test]
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

        $cart = $this->createCartMock();
        $cart->method('getGross')->willReturn(19.99);

        $service->setCart($cart);

        self::assertFalse(
            $service->isFree()
        );
    }

    #[Test]
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

        $cart = $this->createCartMock();
        $cart->method('getGross')->willReturn(100.01);

        $service->setCart($cart);

        self::assertFalse(
            $service->isFree()
        );
    }
    #[Test]
    public function taxClassIdsGreaterZeroReturnsTaxClass(): void
    {
        $cart = $this->createCartMock();
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

    #[Test]
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

        $cart = $this->createCartMock();
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

    #[Test]
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

    private function createCartMock(array $methods = ['getGross']): Cart|MockObject
    {
        GeneralUtility::addInstance(
            CurrencyTranslationServiceInterface::class,
            new CurrencyTranslationService()
        );

        return $this->getMockBuilder(Cart::class)
            ->onlyMethods($methods)
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
    }
}
