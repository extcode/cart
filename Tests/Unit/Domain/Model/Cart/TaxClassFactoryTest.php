<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\TaxClassFactory;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use Psr\Log\NullLogger;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(TaxClassFactory::class)]
final class TaxClassFactoryTest extends UnitTestCase
{
    #[Test]
    public function createsTaxClassFromStringsOfTypoScript(): void
    {
        $taxClass = (new TaxClassFactory(new NullLogger()))->getTaxClass(1, ['value' => '19', 'calc' => '0.19', 'name' => 'normal']);

        self::assertNotNull($taxClass);
        self::assertSame(1, $taxClass->getId());
        self::assertSame('19', $taxClass->getValue());
        self::assertSame(0.19, $taxClass->getCalc());
        self::assertSame('normal', $taxClass->getTitle());
    }

    #[Test]
    public function createsTaxClassFromTypedValuesOfSiteSettings(): void
    {
        $taxClass = (new TaxClassFactory(new NullLogger()))->getTaxClass(3, ['value' => 0, 'calc' => 0.0, 'name' => 'free']);

        self::assertNotNull($taxClass);
        self::assertSame('0', $taxClass->getValue());
        self::assertSame(0.0, $taxClass->getCalc());
        self::assertSame('free', $taxClass->getTitle());
    }

    #[Test]
    public function returnsNullWithoutValue(): void
    {
        self::assertNull((new TaxClassFactory(new NullLogger()))->getTaxClass(1, ['calc' => 0.19, 'name' => 'normal']));
    }
}
