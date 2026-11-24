<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\CartCouponPercentage;
use Extcode\Cart\Domain\Model\Cart\TaxClass as CartTaxClass;
use Extcode\Cart\Domain\Model\Order\Discount;
use Extcode\Cart\Domain\Model\Order\TaxClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\IgnoreDeprecations;
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

    protected function setUp(): void
    {
        $this->taxClass = new TaxClass();
        $this->taxClass->setTitle('normal');
        $this->taxClass->setValue('19');
        $this->taxClass->setCalc(0.19);

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
    public function constructorAcceptsNoTaxClass(): void
    {
        $discount = new Discount('Discount', 'discount', 10.0, 10.0, null, 0.0);

        self::assertNull($discount->getTaxClass());
    }

    #[IgnoreDeprecations]
    #[Test]
    public function constructorIgnoresTheDeprecatedTaxClassOfTheCart(): void
    {
        $this->expectUserDeprecationMessage(
            'Passing a tax class of the cart (' . CartTaxClass::class . ') to ' . Discount::class . ' is deprecated'
            . ' since cart 12.8 and will stop working in cart 13.0. Pass the tax class of the order ('
            . TaxClass::class . ') instead.'
        );

        $discount = new Discount('Discount', 'discount', 10.0, 8.40, new CartTaxClass(1, '19 %', 0.19, 'normal'), 1.60);

        self::assertNull($discount->getTaxClass());
    }

    #[Test]
    public function getTaxInitiallyReturnsTaxSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->tax,
            $this->discount->getTax()
        );
    }

    #[Test]
    public function getCouponTypeInitiallyReturnsEmptyString(): void
    {
        self::assertSame('', $this->discount->getCouponType());
    }

    #[Test]
    public function getCouponTypeReturnsCouponTypeSetByConstructor(): void
    {
        $discount = new Discount('Discount', 'discount', 10.0, 8.40, null, 0.0, CartCouponPercentage::class);

        self::assertSame(CartCouponPercentage::class, $discount->getCouponType());
    }

    #[Test]
    public function getTaxesInitiallyReturnsEmptyArrayAndHasTaxesReturnsFalse(): void
    {
        self::assertSame([], $this->discount->getTaxes());
        self::assertFalse($this->discount->hasTaxes());
    }

    #[Test]
    public function getTaxesReturnsTaxesPerTaxClassSetByConstructor(): void
    {
        $discount = new Discount('Discount', 'discount', 10.0, 7.40, null, 0.0, CartCouponPercentage::class, [1 => 1.9, 2 => 0.7]);

        self::assertSame([1 => 1.9, 2 => 0.7], $discount->getTaxes());
        self::assertTrue($discount->hasTaxes());
    }

    #[Test]
    public function emptyTaxesMarkTheTaxesAsKnown(): void
    {
        $discount = new Discount('Discount', 'discount', 10.0, 10.0, null, 0.0, CartCouponPercentage::class, []);

        self::assertSame([], $discount->getTaxes());
        self::assertTrue($discount->hasTaxes());
    }

    #[Test]
    public function getTaxesReturnsFloatsForStoredIntegers(): void
    {
        $this->discount->_setProperty('taxes', '{"3":2,"4":"0.5"}');

        self::assertSame([3 => 2.0, 4 => 0.5], $this->discount->getTaxes());
    }
}
