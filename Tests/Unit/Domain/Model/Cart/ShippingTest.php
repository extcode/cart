<?php

namespace Extcode\Cart\Tests\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;

class ShippingTest extends UnitTestCase
{
    /**
     * Id
     *
     * @var int
     */
    protected $id;

    /**
     * Name
     *
     * @var string
     */
    protected $name;

    /**
     * Status
     *
     * @var int
     */
    protected $status;

    /**
     * Note
     *
     * @var string
     */
    protected $note;

    /**
     * Is Net Price
     *
     * @var bool
     */
    protected $isNetPrice;

    /**
     * Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass $taxClass
     */
    protected $taxClass;

    /**
     * Shipping
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Shipping $shipping
     */
    protected $shipping;

    /**
     *
     */
    public function setUp()
    {
        $this->taxClass = new \Extcode\Cart\Domain\Model\Cart\TaxClass(1, '19', 0.19, 'normal');

        $this->id = 1;
        $this->name = 'Service';
        $this->status = 0;
        $this->note = 'note';
        $this->isNetPrice = 0;

        $this->shipping = new \Extcode\Cart\Domain\Model\Cart\Shipping(
            $this->id,
            $this->name,
            $this->taxClass,
            $this->status,
            $this->note,
            $this->isNetPrice
        );
    }

    /**
     * @test
     */
    public function getCartProductIdReturnsProductIdSetByConstructor()
    {
        $this->assertSame(
            $this->id,
            $this->shipping->getId()
        );
    }
}
