<?php

namespace Extcode\Cart\Tests\Domain\Model\Product;

/**
 * This file is part of the "cart_products" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use Nimut\TestingFramework\TestCase\UnitTestCase;

class QuantityDiscountTest extends UnitTestCase
{
    /**
     * Product Quantity Discount
     *
     * @var \Extcode\Cart\Domain\Model\Product\QuantityDiscount
     */
    protected $fixture = null;

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->fixture = new \Extcode\Cart\Domain\Model\Product\QuantityDiscount();
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

    /**
     * @test
     */
    public function getQuantityInitiallyReturnsZero()
    {
        $this->assertSame(
            0,
            $this->fixture->getQuantity()
        );
    }

    /**
     * @test
     */
    public function setQuantitySetTheQuantity()
    {
        $quantity = 10;

        $this->fixture->setQuantity($quantity);

        $this->assertSame(
            $quantity,
            $this->fixture->getQuantity()
        );
    }
}
