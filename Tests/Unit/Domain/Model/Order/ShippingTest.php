<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ShippingTest extends UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Shipping
     */
    protected $shipping;

    public function setUp(): void
    {
        $this->shipping = new \Extcode\Cart\Domain\Model\Order\Shipping();
    }

    /**
     * @test
     */
    public function getNameInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->shipping->getName()
        );
    }

    /**
     * @test
     */
    public function setNameSetsName()
    {
        $this->shipping->setName('foo bar');

        $this->assertSame(
            'foo bar',
            $this->shipping->getName()
        );
    }

    /**
     * @test
     */
    public function getStatusInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            'open',
            $this->shipping->getStatus()
        );
    }

    /**
     * @test
     */
    public function setStatusSetsStatus()
    {
        $this->shipping->setStatus('shipped');

        $this->assertSame(
            'shipped',
            $this->shipping->getStatus()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->shipping->getNet()
        );
    }

    /**
     * @test
     */
    public function setNetSetsNet()
    {
        $this->shipping->setNet(1234.56);

        $this->assertSame(
            1234.56,
            $this->shipping->getNet()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->shipping->getGross()
        );
    }

    /**
     * @test
     */
    public function setGrossSetsGross()
    {
        $this->shipping->setGross(1234.56);

        $this->assertSame(
            1234.56,
            $this->shipping->getGross()
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->shipping->getTax()
        );
    }

    /**
     * @test
     */
    public function setTaxSetsTax()
    {
        $this->shipping->setTax(1234.56);

        $this->assertSame(
            1234.56,
            $this->shipping->getTax()
        );
    }

    /**
     * @test
     */
    public function getNoteInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->shipping->getNote()
        );
    }

    /**
     * @test
     */
    public function setNoteSetsNote()
    {
        $this->shipping->setNote('foo bar');

        $this->assertSame(
            'foo bar',
            $this->shipping->getNote()
        );
    }
}
