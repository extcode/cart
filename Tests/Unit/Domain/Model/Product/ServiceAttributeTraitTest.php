<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Product\ServiceAttributeTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(ServiceAttributeTrait::class)]
class ServiceAttributeTraitTest extends UnitTestCase
{
    protected $trait;

    public function setUp(): void
    {
        parent::setUp();

        $this->trait = $this->getObjectForTrait(ServiceAttributeTrait::class);
    }

    #[Test]
    public function getServiceAttribute1ReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->trait->getServiceAttribute1()
        );
    }

    #[Test]
    public function setServiceAttribute1SetsServiceAttribute1(): void
    {
        $serviceAttribute1 = 1.0;

        $this->trait->setServiceAttribute1($serviceAttribute1);

        self::assertSame(
            $serviceAttribute1,
            $this->trait->getServiceAttribute1()
        );
    }

    #[Test]
    public function getServiceAttribute2ReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->trait->getServiceAttribute2()
        );
    }

    #[Test]
    public function setServiceAttribute2SetsServiceAttribute2(): void
    {
        $serviceAttribute2 = 2.0;

        $this->trait->setServiceAttribute2($serviceAttribute2);

        self::assertSame(
            $serviceAttribute2,
            $this->trait->getServiceAttribute2()
        );
    }

    #[Test]
    public function getServiceAttribute3ReturnsZero(): void
    {
        self::assertSame(
            0.0,
            $this->trait->getServiceAttribute3()
        );
    }

    #[Test]
    public function setServiceAttribute3SetsServiceAttribute3(): void
    {
        $serviceAttribute3 = 3.0;

        $this->trait->setServiceAttribute3($serviceAttribute3);

        self::assertSame(
            $serviceAttribute3,
            $this->trait->getServiceAttribute3()
        );
    }
}
