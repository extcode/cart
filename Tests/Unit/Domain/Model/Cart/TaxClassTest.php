<?php

namespace Extcode\Cart\Tests\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;

class TaxClassTest extends UnitTestCase
{
    /**
     * Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $fixture = null;

    /**
     * Id
     *
     * @var int
     */
    private $id;

    /**
     * Value
     *
     * @var string
     */
    private $value;

    /**
     * Calc
     *
     * @var int
     */
    private $calc;

    /**
     * Title
     *
     * @var string
     */
    private $title;

    /**
     * Set Up
     */
    public function setUp()
    {
        $this->id = 1;
        $this->value = '19';
        $this->calc = 0.19;
        $this->title = 'normal Tax';

        $this->fixture = new \Extcode\Cart\Domain\Model\Cart\TaxClass(
            $this->id,
            $this->value,
            $this->calc,
            $this->title
        );
    }

    /**
     * Tear Down
     */
    public function tearDown()
    {
        unset($this->id);
        unset($this->value);
        unset($this->calc);
        unset($this->title);

        unset($this->fixture);
    }

    /**
     * @test
     */
    public function getIdReturnsIdSetByConstructor()
    {
        $this->assertSame(
            $this->id,
            $this->fixture->getId()
        );
    }

    /**
     * @test
     */
    public function getValueReturnsValueSetByConstructor()
    {
        $this->assertSame(
            $this->value,
            $this->fixture->getValue()
        );
    }

    /**
     * @test
     */
    public function getCalcReturnsCalcSetByConstructor()
    {
        $this->assertSame(
            $this->calc,
            $this->fixture->getCalc()
        );
    }

    /**
     * @test
     */
    public function getTitleReturnsNameSetByConstructor()
    {
        $this->assertSame(
            $this->title,
            $this->fixture->getTitle()
        );
    }
}
