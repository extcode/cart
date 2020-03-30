<?php

namespace Extcode\Cart\Tests\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TaxTest extends UnitTestCase
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Tax
     */
    protected $orderTax = null;

    /**
     * Tax
     *
     * @var float
     */
    protected $tax;

    /**
     * TaxClass
     *
     * @var \Extcode\Cart\Domain\Model\Order\TaxClass
     */
    protected $taxClass;

    /**
     *
     */
    public function setUp()
    {
        $this->taxClass = new \Extcode\Cart\Domain\Model\Order\TaxClass('normal', '19', 0.19);

        $this->tax = 10.00;

        $this->orderTax = new \Extcode\Cart\Domain\Model\Order\Tax(
            $this->tax,
            $this->taxClass
        );
    }

    /**
     * @test
     */
    public function constructTaxWithoutTaxThrowsException()
    {
        $this->expectException(\TypeError::class);

        new \Extcode\Cart\Domain\Model\Order\Tax(
            null,
            $this->taxClass
        );
    }

    /**
     * @test
     */
    public function constructTaxWithoutTaxClassThrowsException()
    {
        $this->expectException(\TypeError::class);

        new \Extcode\Cart\Domain\Model\Order\Tax(
            $this->tax,
            null
        );
    }

    /**
     * @test
     */
    public function getTaxInitiallyReturnsTaxSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->tax,
            $this->orderTax->getTax()
        );
    }

    /**
     * @test
     */
    public function getTaxClassInitiallyReturnsTaxClassSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->taxClass,
            $this->orderTax->getTaxClass()
        );
    }
}
