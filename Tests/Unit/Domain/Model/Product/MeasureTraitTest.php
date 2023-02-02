<?php

declare(strict_types=1);

namespace Extcode\Cart\Tests\Unit\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Product\MeasureTrait;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class MeasureTraitTest extends UnitTestCase
{
    protected $trait;

    public function setUp(): void
    {
        parent::setUp();

        $this->trait = $this->getObjectForTrait(MeasureTrait::class);
    }

    /**
     * @test
     */
    public function getPriceMeasure()
    {
        self::assertSame(
            0.0,
            $this->trait->getPriceMeasure()
        );
    }

    /**
     * @test
     */
    public function setPriceMeasureSetsPriceMeasure()
    {
        $priceMeasure = 10.99;

        $this->trait->setPriceMeasure($priceMeasure);

        self::assertSame(
            $priceMeasure,
            $this->trait->getPriceMeasure()
        );
    }

    public function getMeasureUnitsInitiallyReturnsArrayWithWeightsVolumesAndLengths()
    {
        self::assertIsArray(
            $this->trait->getMeasureUnits()
        );
        self::assertArrayHasKey(
            'weight',
            $this->trait->getMeasureUnits()
        );
        self::assertArrayHasKey(
            'volume',
            $this->trait->getMeasureUnits()
        );
        self::assertArrayHasKey(
            'length',
            $this->trait->getMeasureUnits()
        );
    }

    /**
     * @test
     */
    public function setMeasureUnitsSetsMeasureUnits()
    {
        $measureUnits = [
            'weight' => [
                'g' => 1000000,
                'kg' => 1000,
                't' => 1,
            ],
        ];

        $this->trait->setMeasureUnits($measureUnits);

        self::assertSame(
            $measureUnits,
            $this->trait->getMeasureUnits()
        );
    }

    /**
     * @test
     */
    public function getPriceMeasureUnit()
    {
        self::assertSame(
            '',
            $this->trait->getPriceMeasureUnit()
        );
    }

    /**
     * @test
     */
    public function setPriceMeasureUnitSetsPriceMeasureUnit()
    {
        $priceMeasureUnit = 'l';

        $this->trait->setPriceMeasureUnit($priceMeasureUnit);

        self::assertSame(
            $priceMeasureUnit,
            $this->trait->getPriceMeasureUnit()
        );
    }

    /**
     * @test
     */
    public function getBasePriceMeasureUnit()
    {
        self::assertSame(
            '',
            $this->trait->getBasePriceMeasureUnit()
        );
    }

    /**
     * @test
     */
    public function setBasePriceMeasureUnitSetsBasePriceMeasureUnit()
    {
        $priceBaseMeasureUnit = 'l';

        $this->trait->setBasePriceMeasureUnit($priceBaseMeasureUnit);

        self::assertSame(
            $priceBaseMeasureUnit,
            $this->trait->getBasePriceMeasureUnit()
        );
    }

    /**
     * @test
     */
    public function getIsMeasureUnitCompatibilityInitiallyRetrunsFalse()
    {
        self::assertFalse(
            $this->trait->getIsMeasureUnitCompatibility()
        );
    }

    /**
     * @test
     */
    public function getIsMeasureUnitCompatibilityAndNotSetPriceMeasureUnitsRetrunsFalse()
    {
        $this->trait->setBasePriceMeasureUnit('l');

        self::assertFalse(
            $this->trait->getIsMeasureUnitCompatibility()
        );
    }

    /**
     * @test
     */
    public function getIsMeasureUnitCompatibilityAndNotSetBasePriceMeasureUnitsRetrunsFalse()
    {
        self::assertFalse(
            $this->trait->getIsMeasureUnitCompatibility()
        );
    }

    /**
     * Measurement Units Provider
     *
     * @return array
     */
    public static function measureUnitsProvider()
    {
        return [
            ['mg', 'kg', 1000000.0, 1000.0, 1000.0],
            ['g', 'kg', 1000.0, 1000.0, 1.0],
            ['kg', 'kg', 1.0, 1000.0, 0.001],
            ['ml', 'l', 1000.0, 1000.0, 1.0],
            ['cl', 'l', 100.0, 1000.0, 0.1],
            ['l', 'l', 1.0, 1000.0, 0.001],
            ['cbm', 'l', 0.001, 1.0, 0.001],
            ['mm', 'm', 1000.0, 1000.0, 1.0],
            ['cm', 'm', 100.0, 1000.0, 0.1],
            ['m', 'm', 1.0, 2.0, 0.5],
            ['km', 'm', 0.001, 2.0, 0.0005],
            ['m2', 'm2', 1.0, 20.0, 0.05],
        ];
    }

    /**
     * @test
     * @dataProvider measureUnitsProvider
     */
    public function getIsMeasureUnitCompatibilityRetrunsTrueOnSameTypeOfMeasureUnit(
        $sourceMeasureUnit,
        $targetMeasureUnit,
        $factor,
        $priceMeasure,
        $calculatedBasePrice
    ) {
        $this->trait->setPriceMeasureUnit($sourceMeasureUnit);
        $this->trait->setBasePriceMeasureUnit($targetMeasureUnit);

        self::assertTrue(
            $this->trait->getIsMeasureUnitCompatibility()
        );
    }

    /**
     * @test
     * @dataProvider measureUnitsProvider
     */
    public function getMeasureUnitFactorForGivenPriceMeasureUnitAndBasePriceMeasureUnitReturnsFactor(
        $sourceMeasureUnit,
        $targetMeasureUnit,
        $factor,
        $priceMeasure,
        $calculatedBasePrice
    ) {
        $this->trait->setPriceMeasureUnit($sourceMeasureUnit);
        $this->trait->setBasePriceMeasureUnit($targetMeasureUnit);
        $this->trait->setPriceMeasure(1);

        self::assertSame(
            $factor,
            $this->trait->getMeasureUnitFactor()
        );
    }

    /**
     * @test
     * @dataProvider measureUnitsProvider
     */
    public function getCalculatedBasePriceForGivenPriceMeasureUnitAndBasePriceMeasureUnitRetrunsPrice(
        $sourceMeasureUnit,
        $targetMeasureUnit,
        $factor,
        $priceMeasure,
        $calculatedBasePrice
    ) {
        $this->trait->setPriceMeasureUnit($sourceMeasureUnit);
        $this->trait->setBasePriceMeasureUnit($targetMeasureUnit);
        $this->trait->setPriceMeasure($priceMeasure);

        self::assertSame(
            $calculatedBasePrice,
            $this->trait->getMeasureUnitFactor()
        );
    }
}
