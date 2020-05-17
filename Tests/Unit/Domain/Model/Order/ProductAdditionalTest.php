<?php

namespace Extcode\Cart\Tests\Unit\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Nimut\TestingFramework\TestCase\UnitTestCase;

class ProductAdditionalTest extends UnitTestCase
{
    /**
     * Additional Type
     *
     * @var string
     */
    protected $additionalType = '';

    /**
     * Additional Key
     *
     * @var string
     */
    protected $additionalKey = '';

    /**
     * Additional Value
     *
     * @var string
     */
    protected $additionalValue = '';

    /**
     * @var \Extcode\Cart\Domain\Model\Order\ProductAdditional
     */
    protected $productAdditional = null;

    /**
     *
     */
    public function setUp()
    {
        $this->additionalType = 'additional-type';
        $this->additionalKey = 'additional-key';
        $this->additionalValue = 'additional-value';

        $this->productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            $this->additionalType,
            $this->additionalKey,
            $this->additionalValue
        );
    }

    /**
     * @test
     */
    public function constructProductAdditionalWithoutAdditionalTypeThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $additionalType for constructor.',
            1456828210
        );

        $this->productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            null,
            $this->additionalKey,
            $this->additionalValue
        );
    }

    /**
     * @test
     */
    public function constructProductAdditionalWithoutAdditionalKeyThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $additionalKey for constructor.',
            1456828220
        );

        $this->productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            $this->additionalType,
            null,
            $this->additionalValue
        );
    }

    /**
     * @test
     */
    public function constructProductAdditionalWithoutAdditionalValueThrowsException()
    {
        $this->expectException(
            'InvalidArgumentException',
            'You have to specify a valid $additionalValue for constructor.',
            1456828230
        );

        $this->productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            $this->additionalType,
            $this->additionalKey,
            null
        );
    }

    /**
     * @test
     */
    public function getAdditionalTypeInitiallyReturnsAdditionalTypeSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->additionalType,
            $this->productAdditional->getAdditionalType()
        );
    }

    /**
     * @test
     */
    public function getAdditionalKeyInitiallyReturnsAdditionalKeySetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->additionalKey,
            $this->productAdditional->getAdditionalKey()
        );
    }

    /**
     * @test
     */
    public function getAdditionalValueInitiallyReturnsAdditionalValueSetDirectlyByConstructor()
    {
        $this->assertSame(
            $this->additionalValue,
            $this->productAdditional->getAdditionalValue()
        );
    }

    /**
     * @test
     */
    public function getAdditionalDataInitiallyReturnsAdditionalDataSetDirectlyByConstructor()
    {
        $additionalData = 'additional-data';

        $productAdditional = new \Extcode\Cart\Domain\Model\Order\ProductAdditional(
            $this->additionalType,
            $this->additionalKey,
            $this->additionalValue,
            $additionalData
        );

        $this->assertSame(
            $additionalData,
            $productAdditional->getAdditionalData()
        );
    }

    /**
     * @test
     */
    public function getAdditionalDataInitiallyReturnsEmptyString()
    {
        $this->assertSame(
            '',
            $this->productAdditional->getAdditionalData()
        );
    }

    /**
     * @test
     */
    public function setAdditionalDataSetsAdditionalData()
    {
        $additionalData = 'additional-data';

        $this->productAdditional->setAdditionalData($additionalData);

        $this->assertSame(
            $additionalData,
            $this->productAdditional->getAdditionalData()
        );
    }
}
