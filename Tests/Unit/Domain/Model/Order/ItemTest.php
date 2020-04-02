<?php

namespace Extcode\Cart\Tests\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ItemTest extends UnitTestCase
{
    /**
     * @var int
     */
    protected $cartPid = 1;

    /**
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $item;

    /**
     *
     */
    public function setUp()
    {
        $this->item = new \Extcode\Cart\Domain\Model\Order\Item();
    }

    /**
     * @test
     */
    public function getCartPidInitiallyReturnsZero()
    {
        $this->assertSame(
            0,
            $this->item->getCartPid()
        );
    }

    /**
     * @test
     */
    public function setCartPidSetsCartPid()
    {
        $this->item->setCartPid(1);

        $this->assertSame(
            1,
            $this->item->getCartPid()
        );
    }

    /**
     * @test
     */
    public function getCurrencyInitiallyReturnsEuroSignString()
    {
        $this->assertSame(
            '€',
            $this->item->getCurrency()
        );
    }

    /**
     * @test
     */
    public function setCurrencySetsCurrency()
    {
        $this->item->setCurrency('$');

        $this->assertSame(
            '$',
            $this->item->getCurrency()
        );
    }

    /**
     * @test
     */
    public function getCurrencyCodeInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->item->getCurrencyCode()
        );
    }

    /**
     * @test
     */
    public function setCurrencyCodeSetsCurrencyCode()
    {
        $this->item->setCurrencyCode('EUR');

        $this->assertSame(
            'EUR',
            $this->item->getCurrencyCode()
        );
    }

    /**
     * @test
     */
    public function getCurrencySignInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->item->getCurrencySign()
        );
    }

    /**
     * @test
     */
    public function setCurrencySignSetsCurrencySign()
    {
        $this->item->setCurrencySign('€');

        $this->assertSame(
            '€',
            $this->item->getCurrencySign()
        );
    }

    /**
     * @test
     */
    public function getCurrencyTranslationInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            1.00,
            $this->item->getCurrencyTranslation()
        );
    }

    /**
     * @test
     */
    public function setCurrencyTranslationSetsCurrencyTranslation()
    {
        $this->item->setCurrencyTranslation(0.50);

        $this->assertSame(
            0.50,
            $this->item->getCurrencyTranslation()
        );
    }
}
