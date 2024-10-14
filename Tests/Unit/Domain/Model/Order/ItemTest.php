<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

#[CoversClass(Item::class)]
class ItemTest extends UnitTestCase
{
    protected int $cartPid = 1;

    protected Item $item;

    public function setUp(): void
    {
        $this->item = new Item();

        parent::setUp();
    }

    #[Test]
    public function getCartPidInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->item->getCartPid()
        );
    }

    #[Test]
    public function setCartPidSetsCartPid(): void
    {
        $this->item->setCartPid(1);

        self::assertSame(
            1,
            $this->item->getCartPid()
        );
    }

    #[Test]
    public function getCurrencyInitiallyReturnsEuroSignString(): void
    {
        self::assertSame(
            '€',
            $this->item->getCurrency()
        );
    }

    #[Test]
    public function setCurrencySetsCurrency(): void
    {
        $this->item->setCurrency('$');

        self::assertSame(
            '$',
            $this->item->getCurrency()
        );
    }

    #[Test]
    public function getCurrencyCodeInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->item->getCurrencyCode()
        );
    }

    #[Test]
    public function setCurrencyCodeSetsCurrencyCode(): void
    {
        $this->item->setCurrencyCode('EUR');

        self::assertSame(
            'EUR',
            $this->item->getCurrencyCode()
        );
    }

    #[Test]
    public function getCurrencySignInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->item->getCurrencySign()
        );
    }

    #[Test]
    public function setCurrencySignSetsCurrencySign(): void
    {
        $this->item->setCurrencySign('€');

        self::assertSame(
            '€',
            $this->item->getCurrencySign()
        );
    }

    #[Test]
    public function getCurrencyTranslationInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            1.00,
            $this->item->getCurrencyTranslation()
        );
    }

    #[Test]
    public function setCurrencyTranslationSetsCurrencyTranslation(): void
    {
        $this->item->setCurrencyTranslation(0.50);

        self::assertSame(
            0.50,
            $this->item->getCurrencyTranslation()
        );
    }
}
