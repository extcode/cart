<?php

namespace Extcode\Cart\Domain\Model\Cart;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Cart BeVariant Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class BeVariant
{

    /**
     * Id
     *
     * @var string
     */
    protected $id = '';

    /**
     * Product
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Product
     */
    protected $product = null;

    /**
     * BeVariant
     *
     * @var \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    protected $parentBeVariant = null;

    /**
     * Title
     *
     * @var string
     */
    protected $title = '';

    /**
     * Title Delimiter
     *
     * @var string
     */
    protected $titleDelimiter = ' - ';

    /**
     * SKU
     *
     * @var string
     */
    protected $sku = '';

    /**
     * SKU Delimiter
     *
     * @var string
     */
    protected $skuDelimiter = '-';

    /**
     * Price Calc Method
     *
     * @var int
     */
    protected $priceCalcMethod = 0;

    /**
     * Price
     *
     * @var float
     */
    protected $price = 0.0;

    /**
     * Special Price
     *
     * @var float
     */
    protected $specialPrice = null;

    /**
     * Quantity
     *
     * @var int
     */
    protected $quantity = 0;

    /**
     * Variants
     *
     * @var \Extcode\Cart\Domain\Model\Cart\BeVariant[]
     */
    protected $beVariants;

    /**
     * Gross
     *
     * @var float
     */
    protected $gross = 0.0;

    /**
     * Net
     *
     * @var float
     */
    protected $net = 0.0;

    /**
     * Tax
     *
     * @var float
     */
    protected $tax = 0.0;

    /**
     * Is Fe Variant
     *
     * @var bool
     */
    protected $isFeVariant = false;

    /**
     * Number Of Fe Variant
     *
     * @var int
     */
    protected $hasFeVariants;

    /**
     * Min
     *
     * @var int
     */
    protected $min = 0;

    /**
     * Max
     *
     * @var int
     */
    protected $max = 0;

    /**
     * Additional
     *
     * @var array Additional
     */
    protected $additional = [];

    /**
     * stock
     *
     * @var int
     */
    protected $stock = 0;

    /**
     * __construct
     *
     * @param string $id
     * @param \Extcode\Cart\Domain\Model\Cart\Product $product
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $beVariant
     * @param string $title
     * @param string $sku
     * @param int $priceCalcMethod
     * @param float $price
     * @param int $quantity
     */
    public function __construct(
        $id,
        $product = null,
        $beVariant = null,
        $title,
        $sku,
        $priceCalcMethod,
        $price,
        $quantity = 0
    ) {
        if ($product === null && $beVariant === null) {
            throw new \InvalidArgumentException;
        }

        if ($product != null && $beVariant != null) {
            throw new \InvalidArgumentException;
        }

        if (!$title) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $title for constructor.',
                1437166475
            );
        }

        if (!$sku) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $sku for constructor.',
                1437166615
            );
        }

        if (!$quantity) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $quantity for constructor.',
                1437166805
            );
        }

        $this->id = $id;

        if ($product != null) {
            $this->product = $product;
        }

        if ($beVariant != null) {
            $this->parentBeVariant = $beVariant;
        }

        $this->title = $title;
        $this->sku = $sku;
        $this->priceCalcMethod = $priceCalcMethod;
        $this->price = floatval(str_replace(',', '.', $price));
        $this->quantity = $quantity;

        $this->reCalc();
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $variantArr = [
            'id' => $this->id,
            'sku' => $this->sku,
            'title' => $this->title,
            'price_calc_method' => $this->priceCalcMethod,
            'price' => $this->getPrice(),
            'specialPrice' => $this->getSpecialPrice(),
            'taxClass' => $this->getTaxClass(),
            'quantity' => $this->quantity,
            'price_total_gross' => $this->gross,
            'price_total_net' => $this->net,
            'tax' => $this->tax,
            'additional' => $this->additional
        ];

        if ($this->beVariants) {
            $innerVariantArr = [];

            foreach ($this->beVariants as $variant) {
                /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
                array_push($innerVariantArr, [$variant->getId() => $variant->toArray()]);
            }

            array_push($variantArr, ['variants' => $innerVariantArr]);
        }

        return $variantArr;
    }

    /**
     * Gets Product
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Product
     */
    public function getProduct()
    {
        return $this->product;
    }

    /**
     * Gets Parent Variant
     *
     * @return \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    public function getParentBeVariant()
    {
        return $this->parentBeVariant;
    }

    /**
     * Gets Is Net Price
     *
     * @return bool
     */
    public function getIsNetPrice()
    {
        $isNetPrice = false;

        if ($this->getParentBeVariant()) {
            $isNetPrice = $this->getParentBeVariant()->getIsNetPrice();
        } elseif ($this->getProduct()) {
            $isNetPrice = $this->getProduct()->getIsNetPrice();
        }

        return $isNetPrice;
    }

    /**
     * Gets Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Returns the Title Delimiter
     *
     * @return string
     */
    public function getTitleDelimiter()
    {
        return $this->titleDelimiter;
    }

    /**
     * Sets the Title Delimiter
     *
     * @param string $titleDelimiter
     */
    public function setTitleDelimiter($titleDelimiter)
    {
        $this->titleDelimiter = $titleDelimiter;
    }

    /**
     * Gets Title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Gets Complete Title
     *
     * @return string
     */
    public function getCompleteTitle()
    {
        $title = '';

        if ($this->getParentBeVariant()) {
            $title = $this->getParentBeVariant()->getCompleteTitle();
        } elseif ($this->getProduct()) {
            $title = $this->getProduct()->getTitle();
        }

        if ($this->isFeVariant) {
            $title .= $this->titleDelimiter . $this->id;
        } else {
            $title .= $this->titleDelimiter . $this->title;
        }

        return $title;
    }

    /**
     * Gets CompleteTitleWithoutProduct
     *
     * @return string
     */
    public function getCompleteTitleWithoutProduct()
    {
        $title = '';
        $titleDelimiter = '';

        if ($this->getParentBeVariant()) {
            $title = $this->getParentBeVariant()->getCompleteTitleWithoutProduct();
            $titleDelimiter = $this->titleDelimiter;
        }

        if ($this->isFeVariant) {
            $title .= $titleDelimiter . $this->id;
        } else {
            $title .= $titleDelimiter . $this->title;
        }

        return $title;
    }

    /**
     * Gets Price
     *
     * @return float
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     * Returns Special Price
     *
     * @return float
     */
    public function getSpecialPrice()
    {
        return $this->specialPrice;
    }

    /**
     * Sets Special Price
     *
     * @param float $specialPrice
     */
    public function setSpecialPrice($specialPrice)
    {
        $this->specialPrice = $specialPrice;
    }

    /**
     * Returns Best Price (min of Price and Special Price)
     *
     * @return float
     */
    public function getBestPrice()
    {
        $bestPrice = $this->price;

        if ($this->specialPrice &&
            (
                (($this->specialPrice < $bestPrice) && in_array($this->priceCalcMethod, [0, 1, 4, 5])) ||
                (($this->specialPrice > $bestPrice) && in_array($this->priceCalcMethod, [2, 3]))
            )
        ) {
            $bestPrice = $this->specialPrice;
        }

        return $bestPrice;
    }

    /**
     * Gets Discount
     *
     * @return float
     */
    public function getDiscount()
    {
        $discount = $this->getPriceCalculated() - $this->getBestPriceCalculated();

        return $discount;
    }

    /**
     * Returns Special Price
     *
     * @return float
     */
    public function getSpecialPriceDiscount()
    {
        $discount = 0.0;
        if (($this->price != 0.0) && ($this->specialPrice)) {
            $discount = (($this->price - $this->specialPrice) / $this->price) * 100;
        }
        return $discount;
    }

    /**
     * Gets Price Calculated
     *
     * @return float
     */
    public function getPriceCalculated()
    {
        $price = $this->getBestPrice();

        if ($this->getParentBeVariant()) {
            $parentPrice = $this->getParentBeVariant()->getBestPrice();
        } elseif ($this->getProduct()) {
            $parentPrice = $this->getProduct()->getBestPrice($this->getQuantity());
        } else {
            $parentPrice = 0;
        }

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

                    GeneralUtility::callUserFunction($funcRef, $params, $this);
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
     * Gets Best Price Calculated
     *
     * @return float
     */
    public function getBestPriceCalculated()
    {
        $price = $this->getBestPrice();

        if ($this->getParentBeVariant()) {
            $parentPrice = $this->getParentBeVariant()->getBestPrice();
        } elseif ($this->getProduct()) {
            $parentPrice = $this->getProduct()->getBestPrice($this->getQuantity());
        } else {
            $parentPrice = 0;
        }

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

                    GeneralUtility::callUserFunction($funcRef, $params, $this);
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
     * Gets Parent Price
     *
     * @return float
     */
    public function getParentPrice()
    {
        if ($this->priceCalcMethod == 1) {
            return 0.0;
        }

        if ($this->getParentBeVariant()) {
            return $this->getParentBeVariant()->getBestPrice();
        } elseif ($this->getProduct()) {
            return $this->getProduct()->getBestPrice($this->getQuantity());
        }

        return 0.0;
    }

    /**
     * Sets Price
     *
     * @param $price
     */
    public function setPrice($price)
    {
        $this->price = $price;

        $this->reCalc();
    }

    /**
     * Gets Price Calc Method
     *
     * @return int
     */
    public function getPriceCalcMethod()
    {
        return $this->priceCalcMethod;
    }

    /**
     * Sets Price Calc Method
     *
     * @param $priceCalcMethod
     */
    public function setPriceCalcMethod($priceCalcMethod)
    {
        $this->priceCalcMethod = $priceCalcMethod;
    }

    /**
     * Returns the SKU Delimiter
     *
     * @return string
     */
    public function getSkuDelimiter()
    {
        return $this->skuDelimiter;
    }

    /**
     * Sets the SKU Delimiter
     *
     * @param string $skuDelimiter
     */
    public function setSkuDelimiter($skuDelimiter)
    {
        $this->skuDelimiter = $skuDelimiter;
    }

    /**
     * Gets Sku
     *
     * @return string
     */
    public function getSku()
    {
        return $this->sku;
    }

    /**
     * Gets CompleteSku
     *
     * @return string
     */
    public function getCompleteSku()
    {
        $sku = '';

        if ($this->getParentBeVariant()) {
            $sku = $this->getParentBeVariant()->getCompleteSku();
        } elseif ($this->getProduct()) {
            $sku = $this->getProduct()->getSku();
        }

        if ($this->isFeVariant) {
            $sku .= $this->skuDelimiter . $this->id;
        } else {
            $sku .= $this->skuDelimiter . $this->sku;
        }

        return $sku;
    }

    /**
     * Gets CompleteSkuWithoutProduct
     *
     * @return string
     */
    public function getCompleteSkuWithoutProduct()
    {
        $sku = '';

        $skuDelimiter = '';

        if ($this->getParentBeVariant()) {
            $sku = $this->getParentBeVariant()->getCompleteSkuWithoutProduct();
            $skuDelimiter = $this->titleDelimiter;
        }

        if ($this->isFeVariant) {
            $sku .= $skuDelimiter . $this->id;
        } else {
            $sku .= $skuDelimiter . $this->sku;
        }

        return $sku;
    }

    /**
     * Sets Sku
     *
     * @param $sku
     *
     * @retrun void
     */
    public function setSku($sku)
    {
        $this->sku = $sku;
    }

    /**
     * Gets Has Fe Variants
     *
     * @return int
     */
    public function getHasFeVariants()
    {
        return $this->hasFeVariants;
    }

    /**
     * Sets Has Fe Variants
     *
     * @param $hasFeVariants
     */
    public function setHasFeVariants($hasFeVariants)
    {
        $this->hasFeVariants = $hasFeVariants;
    }

    /**
     * Gets Is Fe Variant
     *
     * @return bool
     */
    public function getIsFeVariant()
    {
        return $this->isFeVariant;
    }

    /**
     * Sets Is Fe Variant
     *
     * @param bool $isFeVariant
     */
    public function setIsFeVariant($isFeVariant)
    {
        $this->isFeVariant = $isFeVariant;
    }

    /**
     * Gets Quantity
     *
     * @return int
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Gets Gross
     *
     * @return float
     */
    public function getGross()
    {
        $this->calcGross();
        return $this->gross;
    }

    /**
     * Gets Net
     *
     * @return float
     */
    public function getNet()
    {
        $this->calcNet();
        return $this->net;
    }

    /**
     * Gets Tax
     *
     * @return float
     */
    public function getTax()
    {
        return $this->tax;
    }

    /**
     * Gets TaxClass
     *
     * @return \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    public function getTaxClass()
    {
        if ($this->getParentBeVariant()) {
            $taxClass = $this->getParentBeVariant()->getTaxClass();
        } elseif ($this->getProduct()) {
            $taxClass = $this->getProduct()->getTaxClass();
        }
        return $taxClass;
    }

    /**
     * Sets Quantity
     *
     * @param $newQuantity
     */
    public function setQuantity($newQuantity)
    {
        $this->quantity = $newQuantity;

        $this->reCalc();
    }

    /**
     * @param $newQuantity
     */
    public function changeQuantity($newQuantity)
    {
        $this->quantity = $newQuantity;

        if ($this->beVariants) {
            foreach ($this->beVariants as $beVariant) {
                /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $beVariant */
                $beVariant->changeQuantity($newQuantity);
            }
        }

        $this->reCalc();
    }

    /**
     * @param $variantQuantityArray
     * @internal param $id
     * @internal param $newQuantity
     */
    public function changeVariantsQuantity($variantQuantityArray)
    {
        foreach ($variantQuantityArray as $beVariantId => $quantity) {
            /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $beVariant */
            $beVariant = $this->beVariants[$beVariantId];

            if (is_array($quantity)) {
                $beVariant->changeVariantsQuantity($quantity);
                $this->reCalc();
            } else {
                $beVariant->changeQuantity($quantity);
                $this->reCalc();
            }
        }
    }

    /**
     * @param array $newVariants
     */
    public function addBeVariants($newVariants)
    {
        foreach ($newVariants as $newVariant) {
            $this->addBeVariant($newVariant);
        }
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $newBeVariant
     */
    public function addBeVariant(\Extcode\Cart\Domain\Model\Cart\BeVariant $newBeVariant)
    {
        $newBeVariantId = $newBeVariant->getId();

        /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $beVariant */
        $beVariant = $this->beVariants[$newBeVariantId];

        if ($beVariant) {
            if ($beVariant->getBeVariants()) {
                $beVariant->addBeVariants($newBeVariant->getBeVariants());
            } else {
                $newQuantity = $beVariant->getQuantity() + $newBeVariant->getQuantity();
                $beVariant->setQuantity($newQuantity);
            }
        } else {
            $this->beVariants[$newBeVariantId] = $newBeVariant;
        }

        $this->reCalc();
    }

    /**
     * @return array
     */
    public function getBeVariants()
    {
        return $this->beVariants;
    }

    /**
     * @param $beVariantId
     *
     * @return \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    public function getBeVariantById($beVariantId)
    {
        return $this->beVariants[$beVariantId];
    }

    /**
     * @param $beVariantId
     *
     * @return \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    public function getBeVariant($beVariantId)
    {
        return $this->getBeVariantById($beVariantId);
    }

    /**
     * @param $beVariantsArray
     * @return bool|int
     */
    public function removeBeVariants($beVariantsArray)
    {
        foreach ($beVariantsArray as $beVariantId => $value) {
            /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $beVariant */
            $beVariant = $this->beVariants[$beVariantId];
            if ($beVariant) {
                if (is_array($value)) {
                    $beVariant->removeBeVariants($value);

                    if (!$beVariant->getBeVariants()) {
                        unset($this->beVariants[$beVariantId]);
                    }

                    $this->reCalc();
                } else {
                    unset($this->beVariants[$beVariantId]);

                    $this->reCalc();
                }
            } else {
                return -1;
            }
        }

        return true;
    }

    /**
     */
    protected function calcGross()
    {
        if ($this->getIsNetPrice() == false) {
            if ($this->beVariants) {
                $sum = 0.0;
                foreach ($this->beVariants as $beVariant) {
                    /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $beVariant */
                    $sum += $beVariant->getGross();
                }
                $this->gross = $sum;
            } else {
                $this->gross = $this->getBestPriceCalculated() * $this->quantity;
            }
        } else {
            $this->calcNet();
            $this->calcTax();
            $this->gross = $this->net + $this->tax;
        }
    }

    /**
     */
    protected function calcTax()
    {
        if ($this->getIsNetPrice() == false) {
            $this->calcGross();
            $this->tax = ($this->gross / (1 + $this->getTaxClass()->getCalc())) * ($this->getTaxClass()->getCalc());
        } else {
            $this->calcNet();
            $this->tax = ($this->net * $this->getTaxClass()->getCalc());
        }
    }

    /**
     */
    protected function calcNet()
    {
        if ($this->getIsNetPrice() == true) {
            if ($this->beVariants) {
                $sum = 0.0;
                foreach ($this->beVariants as $beVariant) {
                    /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $beVariant */
                    $sum += $beVariant->getNet();
                }
                $this->net = $sum;
            } else {
                $this->net = $this->getBestPriceCalculated() * $this->quantity;
            }
        } else {
            $this->calcGross();
            $this->calcTax();
            $this->net = $this->gross - $this->tax;
        }
    }

    /**
     */
    protected function reCalc()
    {
        if ($this->beVariants) {
            $quantity = 0;
            foreach ($this->beVariants as $beVariant) {
                /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $beVariant */
                $quantity += $beVariant->getQuantity();
            }

            if ($this->quantity != $quantity) {
                $this->quantity = $quantity;
            }
        }

        if ($this->getIsNetPrice() == false) {
            $this->calcGross();
            $this->calcTax();
            $this->calcNet();
        } else {
            $this->calcNet();
            $this->calcTax();
            $this->calcGross();
        }
    }

    /**
     * @return array
     */
    public function getAdditionalArray()
    {
        return $this->additional;
    }

    /**
     * @param $additional
     */
    public function setAdditionalArray($additional)
    {
        $this->additional = $additional;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function getAdditional($key)
    {
        return $this->additional[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setAdditional($key, $value)
    {
        $this->additional[$key] = $value;
    }

    /**
     * @return int
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin($min)
    {
        if ($min < 0 || $min > $this->max) {
            throw new \InvalidArgumentException;
        }

        $this->min = $min;
    }

    /**
     * @return int
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax($max)
    {
        if ($max < 0 || $max < $this->min) {
            throw new \InvalidArgumentException;
        }

        $this->max = $max;
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
}
