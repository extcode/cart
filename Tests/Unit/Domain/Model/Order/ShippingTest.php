<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Shipping;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Shipping::class)]
class ShippingTest extends UnitTestCase
{
    protected Shipping $shipping;

    public function setUp(): void
    {
        $this->shipping = new Shipping();

        parent::setUp();
    }

    #[Test]
    public function getNameInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->shipping->getName()
        );
    }

    #[Test]
    public function setNameSetsName(): void
    {
        $this->shipping->setName('foo bar');

        self::assertSame(
            'foo bar',
            $this->shipping->getName()
        );
    }

    #[Test]
    public function getStatusInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            'open',
            $this->shipping->getStatus()
        );
    }

    #[Test]
    public function setStatusSetsStatus(): void
    {
        $this->shipping->setStatus('shipped');

        self::assertSame(
            'shipped',
            $this->shipping->getStatus()
        );
    }

    #[Test]
    public function getNetInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->shipping->getNet()
        );
    }

    #[Test]
    public function setNetSetsNet(): void
    {
        $this->shipping->setNet(1234.56);

        self::assertSame(
            1234.56,
            $this->shipping->getNet()
        );
    }

    #[Test]
    public function getGrossInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->shipping->getGross()
        );
    }

    #[Test]
    public function setGrossSetsGross(): void
    {
        $this->shipping->setGross(1234.56);

        self::assertSame(
            1234.56,
            $this->shipping->getGross()
        );
    }

    #[Test]
    public function getTaxInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->shipping->getTax()
        );
    }

    #[Test]
    public function setTaxSetsTax(): void
    {
        $this->shipping->setTax(1234.56);

        self::assertSame(
            1234.56,
            $this->shipping->getTax()
        );
    }

    #[Test]
    public function getNoteInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->shipping->getNote()
        );
    }

    #[Test]
    public function setNoteSetsNote(): void
    {
        $this->shipping->setNote('foo bar');

        self::assertSame(
            'foo bar',
            $this->shipping->getNote()
        );
    }
}
