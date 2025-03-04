<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\TaxClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(TaxClass::class)]
class TaxClassTest extends UnitTestCase
{
    protected TaxClass $taxClass;

    protected string $title;

    protected string $value;

    protected float $calc = 0.0;

    public function setUp(): void
    {
        $this->title = 'normal';
        $this->value = '19 %';
        $this->calc = 0.19;

        $this->taxClass = new TaxClass();

        parent::setUp();
    }

    #[Test]
    public function getTitleInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->taxClass->getTitle()
        );
    }

    #[Test]
    public function setTitleSetsTitle(): void
    {
        $this->taxClass->setTitle('normal');

        self::assertSame(
            'normal',
            $this->taxClass->getTitle()
        );
    }

    #[Test]
    public function getValueInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->taxClass->getValue()
        );
    }

    #[Test]
    public function setValueSetsValue(): void
    {
        $this->taxClass->setValue('19');

        self::assertSame(
            '19',
            $this->taxClass->getValue()
        );
    }

    #[Test]
    public function getCalcInitiallyReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->taxClass->getCalc()
        );
    }

    #[Test]
    public function setCalcSetsCalc(): void
    {
        $this->taxClass->setCalc(0.19);

        self::assertSame(
            0.19,
            $this->taxClass->getCalc()
        );
    }

    #[Test]
    public function toArrayReturnsArray(): void
    {
        $taxClassArray = [
            'title' => '',
            'value' => '',
            'calc' => 0.0,
        ];

        self::assertEquals(
            $taxClassArray,
            $this->taxClass->toArray()
        );

        $this->taxClass->setTitle($this->title);
        $this->taxClass->setValue($this->value);
        $this->taxClass->setCalc($this->calc);

        $taxClassArray = [
            'title' => $this->title,
            'value' => $this->value,
            'calc' => $this->calc,
        ];

        self::assertEquals(
            $taxClassArray,
            $this->taxClass->toArray()
        );
    }
}
