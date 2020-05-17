<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class DiscountTest extends UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Discount
     */
    protected $discount = null;

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
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass = null;

    /**
     * Tax
     *
     * @var float
     */
    protected $tax = 0.0;

    public function setUp(): void
    {
        $this->taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $this->title = 'Discount';
        $this->code = 'discount';
        $this->gross = 10.00;
        $this->net = 8.40;
        $this->tax = 1.60;

        $this->discount = new \Extcode\Cart\Domain\Model\Order\Discount(
            $this->title,
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
    public function constructDiscountWithoutTitleThrowsException()
    {
        $this->expectException(\TypeError::class);

        new \Extcode\Cart\Domain\Model\Order\Discount(
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
    public function constructDiscountWithoutCodeThrowsException()
    {
        $this->expectException(\TypeError::class);

        new \Extcode\Cart\Domain\Model\Order\Discount(
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
    public function constructDiscountWithoutGrossThrowsException()
    {
        $this->expectException(\TypeError::class);

        new \Extcode\Cart\Domain\Model\Order\Discount(
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
    public function constructDiscountWithoutNetThrowsException()
    {
        $this->expectException(\TypeError::class);

        new \Extcode\Cart\Domain\Model\Order\Discount(
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
    public function constructDiscountWithoutTaxClassThrowsException()
    {
        $this->expectException(\TypeError::class);

        new \Extcode\Cart\Domain\Model\Order\Discount(
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
    public function constructDiscountWithoutTaxThrowsException()
    {
        $this->expectException(\TypeError::class);

        new \Extcode\Cart\Domain\Model\Order\Discount(
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
    public function getTitleInitiallyReturnsTitleSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->title,
            $this->discount->getTitle()
        );
    }

    /**
     * @test
     */
    public function getCodeInitiallyReturnsCodeSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->code,
            $this->discount->getCode()
        );
    }

    /**
     * @test
     */
    public function getGrossInitiallyReturnsGrossSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->gross,
            $this->discount->getGross()
        );
    }

    /**
     * @test
     */
    public function getNetInitiallyReturnsNetSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->net,
            $this->discount->getNet()
        );
    }

    /**
     * @test
     */
    public function getTaxClassInitiallyReturnsTaxClassSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->taxClass,
            $this->discount->getTaxClass()
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsTaxSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->tax,
            $this->discount->getTax()
        );
    }
}
