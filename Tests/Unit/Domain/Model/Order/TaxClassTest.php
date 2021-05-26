<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\TaxClass;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class TaxClassTest extends UnitTestCase
{
    /**
     * @var TaxClass
     */
    protected $taxClass;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $value;

    /**
     * @var float
     */
    protected $calc = 0.0;

    public function setUp(): void
    {
        $this->title = 'normal';
        $this->value = '19';
        $this->calc = 0.19;

        $this->taxClass = new TaxClass(
            $this->title,
            $this->value,
            $this->calc
        );
    }

    /**
     * @test
     */
    public function constructTaxClassWithoutTitleThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new TaxClass(
            null,
            $this->value,
            $this->calc
        );
    }

    /**
     * @test
     */
    public function constructTaxClassWithoutValueThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new TaxClass(
            $this->title,
            null,
            $this->calc
        );
    }

    /**
     * @test
     */
    public function constructTaxClassWithoutCalcThrowsException(): void
    {
        $this->expectException(\TypeError::class);

        new TaxClass(
            $this->title,
            $this->value,
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
            $this->taxClass->getTitle()
        );
    }

    /**
     * @test
     */
    public function getValueInitiallyReturnsValueSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->value,
            $this->taxClass->getValue()
        );
    }

    /**
     * @test
     */
    public function getCalcInitiallyReturnsCalcSetDirectlyByConstructor(): void
    {
        self::assertSame(
            $this->calc,
            $this->taxClass->getCalc()
        );
    }

    /**
     * @test
     */
    public function toArrayReturnsArray(): void
    {
        $taxClassArray = [
            'title' => $this->title,
            'value' => $this->value,
            'calc' => $this->calc
        ];

        self::assertEquals(
            $taxClassArray,
            $this->taxClass->toArray()
        );
    }
}
