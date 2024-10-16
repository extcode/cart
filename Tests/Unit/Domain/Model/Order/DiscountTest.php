<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\TaxClass;
use Extcode\Cart\Domain\Model\Order\Discount;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Discount::class)]
class DiscountTest extends UnitTestCase
{
    protected Discount $discount;

    protected string $title = '';

    protected string $code = '';

    protected float $gross = 0.0;

    protected float $net = 0.0;

    protected TaxClass $taxClass;

    protected float $tax = 0.0;

    public function setUp(): void
    {
        $this->taxClass = new TaxClass(1, '19', 0.19, 'normal');

        $this->title = 'Discount';
        $this->code = 'discount';
        $this->gross = 10.00;
        $this->net = 8.40;
        $this->tax = 1.60;

        $this->discount = new Discount(
            $this->title,
            $this->code,
            $this->gross,
            $this->net,
            $this->taxClass,
            $this->tax
        );

        parent::setUp();
    }

    #[Test]
    public function constructDiscountWithoutTitleThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Discount(
            null,
            $this->code,
            $this->gross,
            $this->net,
            $this->taxClass,
            $this->tax
        );
    }

    #[Test]
    public function constructDiscountWithoutCodeThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Discount(
            $this->title,
            null,
            $this->gross,
            $this->net,
            $this->taxClass,
            $this->tax
        );
    }

    #[Test]
    public function constructDiscountWithoutGrossThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Discount(
            $this->title,
            $this->code,
            null,
            $this->net,
            $this->taxClass,
            $this->tax
        );
    }

    #[Test]
    public function constructDiscountWithoutNetThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Discount(
            $this->title,
            $this->code,
            $this->gross,
            null,
            $this->taxClass,
            $this->tax
        );
    }

    #[Test]
    public function constructDiscountWithoutTaxClassThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Discount(
            $this->title,
            $this->code,
            $this->gross,
            $this->net,
            null,
            $this->tax
        );
    }

    #[Test]
    public function constructDiscountWithoutTaxThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Discount(
            $this->title,
            $this->code,
            $this->gross,
            $this->net,
            $this->taxClass,
            null
        );
    }

    #[Test]
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->discount->getTitle()
        );
    }

    #[Test]
    public function getCodeInitiallyReturnsCodeSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->code,
            $this->discount->getCode()
        );
    }

    #[Test]
    public function getGrossInitiallyReturnsGrossSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->gross,
            $this->discount->getGross()
        );
    }

    #[Test]
    public function getNetInitiallyReturnsNetSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->net,
            $this->discount->getNet()
        );
    }

    #[Test]
    public function getTaxClassInitiallyReturnsTaxClassSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->taxClass,
            $this->discount->getTaxClass()
        );
    }

    #[Test]
    public function getTaxInitiallyReturnsTaxSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->tax,
            $this->discount->getTax()
        );
    }
}
