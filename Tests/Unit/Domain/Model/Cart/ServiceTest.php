<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ServiceTest extends UnitTestCase
{
    /**
     * @var int
     */
    protected $id = 1;

    /**
     * @var array
     */
    protected $config = [];

    /**
     * @var array
     */
    protected $taxClasses = [];

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Service
     */
    protected $service;

    public function setUp(): void
    {
        $this->normalTaxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'Normal');
        $this->reducedTaxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(2, '7%', 0.07, 'Reduced');
        $this->freeTaxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(3, '0%', 0.00, 'Free');

        $this->taxClasses = [
            1 => $this->normalTaxClass,
            2 => $this->reducedTaxClass,
            3 => $this->freeTaxClass
        ];

        $this->config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open'
        ];

        $this->service = new \Extcode\Cart\Domain\Model\Cart\Service(
            $this->id,
            $this->config
        );
    }

    /**
     * @test
     */
    public function getServiceIdReturnsServiceIdSetByConstructor()
    {
        $this->assertSame(
            $this->id,
            $this->service->getId()
        );
    }

    /**
     * @test
     */
    public function getServiceConfigReturnsServiceConfigSetByConstructor()
    {
        $this->assertSame(
            $this->config,
            $this->service->getConfig()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithoutUntilAvailableConfigurationReturnsTrue()
    {
        $this->assertTrue(
            $this->service->isAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithCartGrossInRangeReturnsTrue()
    {
        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open',
            'available' => [
                'from' => 20.00,
                'until' => 100.00
            ]
        ];

        $service = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Service::class)
            ->setMethods(['calcAll'])
            ->setConstructorArgs([$this->id, $config])
            ->getMock();
        $service->expects($this->any())->method('calcAll')->will($this->returnValue(null));

        $cart1 = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart1->expects($this->any())->method('getGross')->will($this->returnValue(20.00));
        $service->setCart($cart1);
        $this->assertTrue(
            $service->isAvailable()
        );

        $cart2 = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart2->expects($this->any())->method('getGross')->will($this->returnValue(50.00));
        $service->setCart($cart2);
        $this->assertTrue(
            $service->isAvailable()
        );

        $cart3 = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart3->expects($this->any())->method('getGross')->will($this->returnValue(100.00));
        $service->setCart($cart3);
        $this->assertTrue(
            $service->isAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithCartGrossBelowRangeReturnsFalse()
    {
        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open',
            'available' => [
                'from' => 20.00,
                'until' => 100.00
            ]
        ];

        $service = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Service::class)
            ->setMethods(['calcAll'])
            ->setConstructorArgs([$this->id, $config])
            ->getMock();
        $service->expects($this->any())->method('calcAll')->will($this->returnValue(null));

        $cart = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->expects($this->any())->method('getGross')->will($this->returnValue(19.99));

        $service->setCart($cart);

        $this->assertFalse(
            $service->isAvailable()
        );
    }

    /**
     * @test
     */
    public function isAvailableWithCartGrossAboveRangeReturnsFalse()
    {
        $config = [
            'title' => 'Standard',
            'extra' => 0.00,
            'taxClassId' => 1,
            'status' => 'open',
            'available' => [
                'from' => 20.00,
                'until' => 100.00
            ]
        ];

        $service = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Service::class)
            ->setMethods(['calcAll'])
            ->setConstructorArgs([$this->id, $config])
            ->getMock();
        $service->expects($this->any())->method('calcAll')->will($this->returnValue(null));

        $cart = $this->getMockBuilder(\Extcode\Cart\Domain\Model\Cart\Cart::class)
            ->setMethods(['getGross'])
            ->setConstructorArgs([$this->taxClasses])
            ->getMock();
        $cart->expects($this->any())->method('getGross')->will($this->returnValue(100.01));

        $service->setCart($cart);

        $this->assertFalse(
            $service->isAvailable()
        );
    }
}
