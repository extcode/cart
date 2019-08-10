<?php

namespace Extcode\Cart\Tests\Domain\Model\Product;

/**
 * This file is part of the "cart_products" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Nimut\TestingFramework\TestCase\UnitTestCase;

class SpecialPriceTest extends UnitTestCase
{
    /**
     * Product Special Price
     *
     * @var \Extcode\Cart\Domain\Model\Product\SpecialPrice
     */
    protected $fixture = null;

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->fixture = new \Extcode\Cart\Domain\Model\Product\SpecialPrice();
    }

    /**
     * @test
     */
    public function getTitleInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->fixture->getTitle()
        );
    }

    /**
     * @test
     */
    public function setTitleSetsTitle()
    {
        $title = 'Special Price Title';

        $this->fixture->setTitle($title);

        $this->assertSame(
            $title,
            $this->fixture->getTitle()
        );
    }

    /**
     * @test
     */
    public function getPriceInitiallyReturnsZero()
    {
        $this->assertSame(
            0.0,
            $this->fixture->getPrice()
        );
    }

    /**
     * @test
     */
    public function setPriceSetThePrice()
    {
        $price = 1.00;

        $this->fixture->setPrice($price);

        $this->assertSame(
            $price,
            $this->fixture->getPrice()
        );
    }
}
