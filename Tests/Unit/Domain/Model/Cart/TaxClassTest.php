<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\TaxClass;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(TaxClass::class)]
class TaxClassTest extends UnitTestCase
{
    protected TaxClass $fixture;

    private int $id;

    private string $value;

    private float $calc;

    private string $title;

    public function setUp(): void
    {
        $this->id = 1;
        $this->value = '19';
        $this->calc = 0.19;
        $this->title = 'normal Tax';

        $this->fixture = new TaxClass(
            $this->id,
            $this->value,
            $this->calc,
            $this->title
        );

        parent::setUp();
    }

    public function tearDown(): void
    {
        unset(
            $this->id,
            $this->value,
            $this->calc,
            $this->title,
            $this->fixture
        );

        parent::tearDown();
    }

    #[Test]
    public function getIdReturnsIdSetByConstructor(): void
    {
        self::assertSame(
            $this->id,
            $this->fixture->getId()
        );
    }

    #[Test]
    public function getValueReturnsValueSetByConstructor(): void
    {
        self::assertSame(
            $this->value,
            $this->fixture->getValue()
        );
    }

    #[Test]
    public function getCalcReturnsCalcSetByConstructor(): void
    {
        self::assertSame(
            $this->calc,
            $this->fixture->getCalc()
        );
    }

    #[Test]
    public function getTitleReturnsNameSetByConstructor(): void
    {
        self::assertSame(
            $this->title,
            $this->fixture->getTitle()
        );
    }
}
