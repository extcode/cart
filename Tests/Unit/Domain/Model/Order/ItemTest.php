<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ItemTest extends UnitTestCase
{
    /**
     * @var int
     */
    protected $cartPid = 1;

    /**
     * @var Item
     */
    protected $item;

    public function setUp(): void
    {
        $this->item = new Item();

        parent::setUp();
    }

    /**
     * @test
     */
    public function getCartPidInitiallyReturnsZero(): void
    {
        self::assertSame(
            0,
            $this->item->getCartPid()
        );
    }

    /**
     * @test
     */
    public function setCartPidSetsCartPid(): void
    {
        $this->item->setCartPid(1);

        self::assertSame(
            1,
            $this->item->getCartPid()
        );
    }

    /**
     * @test
     */
    public function getCurrencyInitiallyReturnsEuroSignString(): void
    {
        self::assertSame(
            '€',
            $this->item->getCurrency()
        );
    }

    /**
     * @test
     */
    public function setCurrencySetsCurrency(): void
    {
        $this->item->setCurrency('$');

        self::assertSame(
            '$',
            $this->item->getCurrency()
        );
    }

    /**
     * @test
     */
    public function getCurrencyCodeInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->item->getCurrencyCode()
        );
    }

    /**
     * @test
     */
    public function setCurrencyCodeSetsCurrencyCode(): void
    {
        $this->item->setCurrencyCode('EUR');

        self::assertSame(
            'EUR',
            $this->item->getCurrencyCode()
        );
    }

    /**
     * @test
     */
    public function getCurrencySignInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            '',
            $this->item->getCurrencySign()
        );
    }

    /**
     * @test
     */
    public function setCurrencySignSetsCurrencySign(): void
    {
        $this->item->setCurrencySign('€');

        self::assertSame(
            '€',
            $this->item->getCurrencySign()
        );
    }

    /**
     * @test
     */
    public function getCurrencyTranslationInitiallyReturnsEmptyString(): void
    {
        self::assertSame(
            1.00,
            $this->item->getCurrencyTranslation()
        );
    }

    /**
     * @test
     */
    public function setCurrencyTranslationSetsCurrencyTranslation(): void
    {
        $this->item->setCurrencyTranslation(0.50);

        self::assertSame(
            0.50,
            $this->item->getCurrencyTranslation()
        );
    }
}
