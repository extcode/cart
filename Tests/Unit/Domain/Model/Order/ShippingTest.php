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
use Extcode\Cart\OrderStatus\ShippingStatus;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Shipping::class)]
class ShippingTest extends UnitTestCase
{
    protected Shipping $shipping;

    protected function setUp(): void
    {
        $this->shipping = new Shipping();

        parent::setUp();
    }

    #[DataProvider('statusDataProvider')]
    #[Test]
    public function statusMethodsReturnWhetherTheShippingHasTheStatus(
        string $status,
        bool $isOpen,
        bool $isOnHold,
        bool $isInProcess,
        bool $isShipped,
    ): void {
        $this->shipping->setStatus($status);

        self::assertSame($isOpen, $this->shipping->isOpen());
        self::assertSame($isOnHold, $this->shipping->isOnHold());
        self::assertSame($isInProcess, $this->shipping->isInProcess());
        self::assertSame($isShipped, $this->shipping->isShipped());
    }

    /**
     * @return iterable<string, array{string, bool, bool, bool, bool}>
     */
    public static function statusDataProvider(): iterable
    {
        yield 'open' => [ShippingStatus::OPEN, true, false, false, false];
        yield 'on hold' => [ShippingStatus::ON_HOLD, false, true, false, false];
        yield 'in process' => [ShippingStatus::IN_PROCESS, false, false, true, false];
        yield 'shipped' => [ShippingStatus::SHIPPED, false, false, false, true];
        yield 'status of another extension' => ['delivered', false, false, false, false];
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
    public function getStatusInitiallyReturnsOpen(): void
    {
        self::assertSame(
            ShippingStatus::OPEN,
            $this->shipping->getStatus()
        );
    }

    #[Test]
    public function setStatusSetsStatus(): void
    {
        $this->shipping->setStatus(ShippingStatus::SHIPPED);

        self::assertSame(
            ShippingStatus::SHIPPED,
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
