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
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class DiscountTest extends UnitTestCase
{
    /**
     * @var Discount
     */
    protected $discount;

    /**
     * Title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Code
     *
     * @var string
     */
    protected $code = '';

    /**
     * Gross
     *
     * @var float
     */
    protected $gross = 0.0;

    /**
     * Net
     *
     * @var float
     */
    protected $net = 0.0;

    /**
     * TaxClass
     *
     * @var TaxClass
     */
    protected $taxClass;

    /**
     * Tax
     *
     * @var float
     */
    protected $tax = 0.0;

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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
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

    /**
     * @test
     */
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->discount->getTitle()
        );
    }

    /**
     * @test
     */
    public function getCodeInitiallyReturnsCodeSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->code,
            $this->discount->getCode()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsGrossSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->gross,
            $this->discount->getGross()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsNetSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->net,
            $this->discount->getNet()
        );
    }

    /**
     * @test
     */
    public function getTaxClassInitiallyReturnsTaxClassSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->taxClass,
            $this->discount->getTaxClass()
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsTaxSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->tax,
            $this->discount->getTax()
        );
    }
}
