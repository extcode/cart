<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Tax;
use Extcode\Cart\Domain\Model\Order\TaxClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Tax::class)]
class TaxTest extends UnitTestCase
{
    /**
     * @var Tax
     */
    protected $orderTax;

    /**
     * @var float
     */
    protected $tax;

    /**
     * @var TaxClass
     */
    protected $taxClass;

    public function setUp(): void
    {
        $this->taxClass = new TaxClass();
        $this->taxClass->setTitle('normal');
        $this->taxClass->setValue('19');
        $this->taxClass->setCalc(0.19);

        $this->tax = 10.00;

        $this->orderTax = new Tax(
            $this->tax,
            $this->taxClass
        );

        parent::setUp();
    }

    #[Test]
    public function constructTaxWithoutTaxThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Tax(
            null,
            $this->taxClass
        );
    }

    #[Test]
    public function constructTaxWithoutTaxClassThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new Tax(
            $this->tax,
            null
        );
    }

    #[Test]
    public function getTaxInitiallyReturnsTaxSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->tax,
            $this->orderTax->getTax()
        );
    }

    #[Test]
    public function getTaxClassInitiallyReturnsTaxClassSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->taxClass,
            $this->orderTax->getTaxClass()
        );
    }
}
