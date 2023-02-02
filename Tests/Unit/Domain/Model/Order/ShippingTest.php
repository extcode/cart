<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Shipping;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ShippingTest extends UnitTestCase
{
    /**
     * @var Shipping
     */
    protected $shipping;

    public function setUp(): void
    {
        $this->shipping = new Shipping();

        parent::setUp();
    }

    /**
     * @test
     */
    public function getNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->shipping->getName()
        );
    }

    /**
     * @test
     */
    public function setNameSetsName(): void
    {
        $this->shipping->setName('foo bar');

        self::assertSame(
            'foo bar',
            $this->shipping->getName()
        );
    }

    /**
     * @test
     */
    public function getStatusInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            'open',
            $this->shipping->getStatus()
        );
    }

    /**
     * @test
     */
    public function setStatusSetsStatus(): void
    {
        $this->shipping->setStatus('shipped');

        self::assertSame(
            'shipped',
            $this->shipping->getStatus()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->shipping->getNet()
        );
    }

    /**
     * @test
     */
    public function setNetSetsNet(): void
    {
        $this->shipping->setNet(1234.56);

        self::assertSame(
            1234.56,
            $this->shipping->getNet()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->shipping->getGross()
        );
    }

    /**
     * @test
     */
    public function setGrossSetsGross(): void
    {
        $this->shipping->setGross(1234.56);

        self::assertSame(
            1234.56,
            $this->shipping->getGross()
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->shipping->getTax()
        );
    }

    /**
     * @test
     */
    public function setTaxSetsTax(): void
    {
        $this->shipping->setTax(1234.56);

        self::assertSame(
            1234.56,
            $this->shipping->getTax()
        );
    }

    /**
     * @test
     */
    public function getNoteInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->shipping->getNote()
        );
    }

    /**
     * @test
     */
    public function setNoteSetsNote(): void
    {
        $this->shipping->setNote('foo bar');

        self::assertSame(
            'foo bar',
            $this->shipping->getNote()
        );
    }
}
