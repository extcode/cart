<?php

namespace Extcode\Cart\Domain\Model\Product;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Product BeVariant Model
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class BeVariant extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * Product
     *
     * @var \Extcode\Cart\Domain\Model\Product\Product
     */
    protected $product = null;

    /**
     * Variant Attribute 1
     *
     * @var \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption
     */
    protected $beVariantAttributeOption1 = null;

    /**
     * Variant Attribute 2
     *
     * @var \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption
     */
    protected $beVariantAttributeOption2 = null;

    /**
     * Variant Attribute 3
     *
     * @var \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption
     */
    protected $beVariantAttributeOption3 = null;

    /**
     * Price
     *
     * @var float
     */
    protected $price = 0.0;

    /**
     * Price Calc Method
     *
     * @var int
     */
    protected $priceCalcMethod = 0;

    /**
     * Price Measure
     *
     * @var float
     */
    protected $priceMeasure = 0.0;

    /**
     * Price Measure Unit
     *
     * @var string
     */
    protected $priceMeasureUnit = '';

    /**
     * stock
     *
     * @var int
     */
    protected $stock = 0;

    /**
     * Returns the Product
     *
     * @return \Extcode\Cart\Domain\Model\Product\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Returns the Variant Attribute 1
     *
     * @return \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption
     */
    public function getBeVariantAttributeOption1()
    {
        return $this->beVariantAttributeOption1;
    }

    /**
     * Sets the Variant Attribute 1
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption $beVariantAttributeOption1
     * @return void
     */
    public function setBeVariantAttributeOption1(
        \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption $beVariantAttributeOption1
    ) {
        $this->beVariantAttributeOption1 = $beVariantAttributeOption1;
    }

    /**
     * Returns the Variant Attribute 2
     *
     * @return \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption
     */
    public function getBeVariantAttributeOption2()
    {
        return $this->beVariantAttributeOption2;
    }

    /**
     * Sets the Variant Attribute 2
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption $beVariantAttributeOption2
     * @return void
     */
    public function setBeVariantAttributeOption2(
        \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption $beVariantAttributeOption2
    ) {
        $this->beVariantAttributeOption2 = $beVariantAttributeOption2;
    }

    /**
     * Returns the Variant Attribute 3
     *
     * @return \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption
     */
    public function getBeVariantAttributeOption3()
    {
        return $this->beVariantAttributeOption3;
    }

    /**
     * Sets the Variant Attribute 3
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption $beVariantAttributeOption3
     * @return void
     */
    public function setBeVariantAttributeOption3(
        \Extcode\Cart\Domain\Model\Product\BeVariantAttributeOption $beVariantAttributeOption3
    ) {
        $this->beVariantAttributeOption3 = $beVariantAttributeOption3;
    }

    /**
     * Gets Price Calculated
     *
     * @return float
     */
    public function getPriceCalculated()
    {
        $price = $this->getPrice();

        $parentPrice = $this->getProduct()->getPrice();

        switch ($this->priceCalcMethod) {
            case 3:
                $calc_price = -1 * (($price / 100) * ($parentPrice));
                break;
            case 5:
                $calc_price = ($price / 100) * ($parentPrice);
                break;
            default:
                $calc_price = 0;
        }

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['changeVariantDiscount']) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['changeVariantDiscount'] as $funcRef) {
                if ($funcRef) {
                    $params = [
                        'price_calc_method' => $this->priceCalcMethod,
                        'price' => &$price,
                        'parent_price' => &$parentPrice,
                        'calc_price' => &$calc_price,
                    ];

                    \TYPO3\CMS\Core\Utility\GeneralUtility::callUserFunction($funcRef, $params, $this);
                }
            }
        }

        switch ($this->priceCalcMethod) {
            case 1:
                $parentPrice = 0.0;
                break;
            case 2:
                $price = -1 * $price;
                break;
            case 4:
                break;
            default:
                $price = 0.0;
        }

        return $parentPrice + $price + $calc_price;
    }

    /**
     * Gets Price Calculated
     *
     * @return float
     */
    public function getBestPriceCalculated()
    {
        $price = $this->getPrice();

        $parentPrice = $this->getProduct()->getBestSpecialPrice();

        switch ($this->priceCalcMethod) {
            case 3:
                $calc_price = -1 * (($price / 100) * ($parentPrice));
                break;
            case 5:
                $calc_price = ($price / 100) * ($parentPrice);
                break;
            default:
                $calc_price = 0;
        }

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['changeVariantDiscount']) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['changeVariantDiscount'] as $funcRef) {
                if ($funcRef) {
                    $params = [
                        'price_calc_method' => $this->priceCalcMethod,
                        'price' => &$price,
                        'parent_price' => &$parentPrice,
                        'calc_price' => &$calc_price,
                    ];

                    \TYPO3\CMS\Core\Utility\GeneralUtility::callUserFunction($funcRef, $params, $this);
                }
            }
        }

        switch ($this->priceCalcMethod) {
            case 1:
                $parentPrice = 0.0;
                break;
            case 2:
                $price = -1 * $price;
                break;
            case 4:
                break;
            default:
                $price = 0.0;
        }

        return $parentPrice + $price + $calc_price;
    }

    /**
     * Returns the price
     *
     * @return float $price
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Sets the price
     *
     * @param float $price
     * @return void
     */
    public function setPrice($price)
    {
        $this->price = $price;
    }

    /**
     * @return int
     */
    public function getPriceCalcMethod()
    {
        return $this->priceCalcMethod;
    }

    /**
     * @param int $priceCalcMethod
     * @return void
     */
    public function setPriceCalcMethod($priceCalcMethod)
    {
        $this->priceCalcMethod = $priceCalcMethod;
    }

    /**
     * Returns the Base Price
     *
     * @return float $price
     */
    public function getBasePrice()
    {
        #TODO: respects different measuring units between variant and product
        if (!$this->getProduct()->getBasePriceMeasure() > 0) {
            return null;
        }
        if (!$this->getPriceMeasure() > 0) {
            return null;
        }
        $ratio = $this->getProduct()->getBasePriceMeasure() / $this->getPriceMeasure();

        $price = round($this->price * $ratio * 100.0) / 100.0;

        return $price;
    }

    /**
     * Returns the Price Measure
     *
     * @return float $priceMeasure
     */
    public function getPriceMeasure()
    {
        return $this->priceMeasure;
    }

    /**
     * Sets the Price Measure
     *
     * @param float $priceMeasure
     * @return void
     */
    public function setPriceMeasure($priceMeasure)
    {
        $this->priceMeasure = $priceMeasure;
    }

    /**
     * Returns the Price Measure Unit
     *
     * @return string $priceMeasureUnit
     */
    public function getPriceMeasureUnit()
    {
        return $this->priceMeasureUnit;
    }

    /**
     * Sets the Price Measure Unit
     *
     * @param string $priceMeasureUnit
     * @return void
     */
    public function setPriceMeasureUnit($priceMeasureUnit)
    {
        $this->priceMeasureUnit = $priceMeasureUnit;
    }

    /**
     * Check Measure Unit Compatibility
     *
     * @return bool
     */
    public function getIsMeasureUnitCompatibility()
    {
        foreach ($this->product->getMeasureUnits() as $measureUnit) {
            if (array_key_exists($this->product->getBasePriceMeasureUnit(), $measureUnit)
                && array_key_exists($this->priceMeasureUnit, $measureUnit)
            ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get Measure Unit Faktor
     *
     * @return bool
     */
    public function getMeasureUnitFactor()
    {
        $factor = 1.0;

        foreach ($this->product->getMeasureUnits() as $measureUnit) {
            if ($measureUnit[$this->priceMeasureUnit]) {
                $factor = $factor / ($this->priceMeasure / $measureUnit[$this->priceMeasureUnit]);
            }
            if ($measureUnit[$this->product->getBasePriceMeasureUnit()]) {
                $factor = $factor * (1 / $measureUnit[$this->product->getBasePriceMeasureUnit()]);
            }
        }

        return $factor;
    }

    /**
     * Returns Calculated Base Price
     *
     * @return float|bool
     */
    public function getCalculatedBasePrice()
    {
        if ($this->getIsMeasureUnitCompatibility()) {
            return $this->getBestPriceCalculated() * $this->getMeasureUnitFactor();
        }

        return false;
    }

    /**
     * Returns the Stock
     *
     * @return int
     */
    public function getStock()
    {
        return $this->stock;
    }

    /**
     * Set the Stock
     *
     * @param int $stock
     */
    public function setStock($stock)
    {
        $this->stock = $stock;
    }

    /**
     * Returns the calculated SKU
     *
     * @return string
     */
    public function getSku()
    {
        $skuArray = [];

        if ($this->getProduct()->getBeVariantAttribute1()) {
            $skuArray[] = $this->getProduct()->getBeVariantAttribute1()->getSku();
            $skuArray[] = $this->getBeVariantAttributeOption1()->getSku();
        }

        if ($this->getProduct()->getBeVariantAttribute2()) {
            $skuArray[] = $this->getProduct()->getBeVariantAttribute2()->getSku();
            $skuArray[] = $this->getBeVariantAttributeOption2()->getSku();
        }

        if ($this->getProduct()->getBeVariantAttribute3()) {
            $skuArray[] = $this->getProduct()->getBeVariantAttribute3()->getSku();
            $skuArray[] = $this->getBeVariantAttributeOption3()->getSku();
        }

        return join('-', $skuArray);
    }

    /**
     * Returns the calculated Title
     *
     * @return string
     */
    public function getTitle()
    {
        $titleArray = [];

        if ($this->getProduct()->getBeVariantAttribute1()) {
            $titleArray[] = $this->getBeVariantAttributeOption1()->getTitle();
        }

        if ($this->getProduct()->getBeVariantAttribute2()) {
            $titleArray[] = $this->getBeVariantAttributeOption2()->getTitle();
        }

        if ($this->getProduct()->getBeVariantAttribute3()) {
            $titleArray[] = $this->getBeVariantAttributeOption3()->getTitle();
        }

        return join(' ', $titleArray);
    }
}
