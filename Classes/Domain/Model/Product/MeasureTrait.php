<?php

namespace Extcode\Cart\Domain\Model\Product;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

trait MeasureTrait
{
    protected float $priceMeasure = 0.0;

    protected array $measureUnits = [
        'weight' => [
            'mg' => 1000,
            'g' => 1,
            'kg' => 0.001,
        ],
        'volume' => [
            'ml' => 1000,
            'cl' => 100,
            'l' => 1,
            'cbm' => 0.001,
        ],
        'length' => [
            'mm' => 1000,
            'cm' => 100,
            'm' => 1,
            'km' => 0.001,
        ],
        'area' => [
            'm2' => 1,
        ],
    ];

    protected string $priceMeasureUnit = '';

    protected string $basePriceMeasureUnit = '';

    public function getPriceMeasure(): float
    {
        return $this->priceMeasure;
    }

    public function setPriceMeasure(float $priceMeasure): void
    {
        $this->priceMeasure = $priceMeasure;
    }

    public function getMeasureUnits(): array
    {
        return $this->measureUnits;
    }

    public function setMeasureUnits(array $measureUnits): void
    {
        $this->measureUnits = $measureUnits;
    }

    public function getPriceMeasureUnit(): string
    {
        return $this->priceMeasureUnit;
    }

    public function setPriceMeasureUnit(string $priceMeasureUnit): void
    {
        $this->priceMeasureUnit = $priceMeasureUnit;
    }

    public function getBasePriceMeasureUnit(): string
    {
        return $this->basePriceMeasureUnit;
    }

    public function setBasePriceMeasureUnit(string $basePriceMeasureUnit): void
    {
        $this->basePriceMeasureUnit = $basePriceMeasureUnit;
    }

    public function getIsMeasureUnitCompatibility(): bool
    {
        return $this->isMeasureUnitCompatibility();
    }

    public function isMeasureUnitCompatibility(): bool
    {
        foreach ($this->measureUnits as $measureUnitGroup) {
            if (
                isset($measureUnitGroup[$this->priceMeasureUnit]) &&
                isset($measureUnitGroup[$this->basePriceMeasureUnit])
            ) {
                return true;
            }
        }

        return false;
    }

    public function getMeasureUnitFactor(): float
    {
        $factor = 1.0;

        foreach ($this->measureUnits as $measureUnit) {
            if (array_key_exists($this->priceMeasureUnit, $measureUnit)) {
                $factor = $factor / ($this->priceMeasure / $measureUnit[$this->priceMeasureUnit]);
            }
            if (array_key_exists($this->basePriceMeasureUnit, $measureUnit)) {
                $factor = $factor * (1.0 / $measureUnit[$this->basePriceMeasureUnit]);
            }
        }

        return $factor;
    }
}
