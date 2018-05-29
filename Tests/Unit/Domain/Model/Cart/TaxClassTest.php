<?php

namespace Extcode\Cart\Tests\Domain\Model\Cart;

/**
 * This file is part of the "cart_products" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
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
    public function constructTaxClassWithoutIdThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $id for constructor.',
            1413981328
        );

        new \Extcode\Cart\Domain\Model\Cart\TaxClass(
            null,
            $this->value,
            $this->calc,
            $this->title
        );
    }

    /**
     * @test
     */
    public function constructTaxClassWithoutValueThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $value for constructor.',
            1413981329
        );

        new \Extcode\Cart\Domain\Model\Cart\TaxClass(
            $this->id,
            null,
            $this->calc,
            $this->title
        );
    }

    /**
     * @test
     */
    public function constructTaxClassWithoutCalcThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $calc for constructor.',
            1413981330
        );

        new \Extcode\Cart\Domain\Model\Cart\TaxClass(
            $this->id,
            $this->value,
            null,
            $this->title
        );
    }

    /**
     * @test
     */
    public function constructTaxClassWithoutTitleThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $title for constructor.',
            1413981331
        );

        new \Extcode\Cart\Domain\Model\Cart\TaxClass(
            $this->id,
            $this->value,
            $this->calc,
            null
        );
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
