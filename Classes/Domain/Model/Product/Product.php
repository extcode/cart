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
 * Product Product Model
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Product extends \Extcode\Cart\Domain\Model\Product\AbstractProduct
{

    /**
     * Measurement Units
     *
     * @var array
     */
    protected $measureUnits = [
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
        ]
    ];

    /**
     * Product Type
     *
     * @var string
     */
    protected $productType = 'simple';

    /**
     * Images
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $images;

    /**
     * Files
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference>
     */
    protected $files;

    /**
     * Teaser
     *
     * @var string
     */
    protected $teaser = '';

    /**
     * Product Content
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\TtContent>
     * @lazy
     */
    protected $productContent;

    /**
     * Min Number In Order
     *
     * @var integer
     */
    protected $minNumberInOrder = 0;

    /**
     * Max Number in Order
     *
     * @var integer
     */
    protected $maxNumberInOrder = 0;

    /**
     * Price
     *
     * @var float
     */
    protected $price = 0.0;

    /**
     * Product Special Price
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\SpecialPrice>
     * @cascade remove
     */
    protected $specialPrices;

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
     * Base Price Measure Unit
     *
     * @var string
     */
    protected $basePriceMeasureUnit = '';

    /**
     * Service Attriute 1
     *
     * @var float
     */
    protected $serviceAttribute1 = 0.0;

    /**
     * Service Attriute 2
     *
     * @var float
     */
    protected $serviceAttribute2 = 0.0;

    /**
     * Service Attriute 3
     *
     * @var float
     */
    protected $serviceAttribute3 = 0.0;

    /**
     * Tax Class Id
     *
     * @var integer
     */
    protected $taxClassId = 1;

    /**
     * beVariantAttribute1
     *
     * @var \Extcode\Cart\Domain\Model\Product\BeVariantAttribute
     */
    protected $beVariantAttribute1 = null;

    /**
     * beVariantAttribute2
     *
     * @var \Extcode\Cart\Domain\Model\Product\BeVariantAttribute
     */
    protected $beVariantAttribute2 = null;

    /**
     * beVariantAttribute3
     *
     * @var \Extcode\Cart\Domain\Model\Product\BeVariantAttribute
     */
    protected $beVariantAttribute3 = null;

    /**
     * variants
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\BeVariant>
     * @cascade remove
     */
    protected $beVariants = null;

    /**
     * Frontend Variants
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\FeVariant>
     * @cascade remove
     */
    protected $feVariants = null;

    /**
     * Related Products
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\Product>
     * @lazy
     */
    protected $relatedProducts = null;

    /**
     * Related Products (from)
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\Product>
     * @lazy
     */
    protected $relatedProductsFrom;

    /**
     * stock
     *
     * @var int
     */
    protected $stock = 0;

    /**
     * Handle Stock
     *
     * @var bool
     */
    protected $handleStock = false;

    /**
     * Categories
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Category>
     */
    protected $categories = null;

    /**
     * Tags
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\Tag>
     */
    protected $tags;

    /**
     * Product constructor.
     *
     */
    public function __construct()
    {
        $this->specialPrices = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
        $this->beVariants = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the Product Type
     *
     * @return string
     */
    public function getProductType()
    {
        return $this->productType;
    }

    /**
     * Set the Product Type
     *
     * @var string $productType
     * @return void
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;
    }

    /**
     * Returns the Images
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     */
    public function getImages()
    {
        return $this->images;
    }

    /**
     * Returns the first Image
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function getFirstImage()
    {
        return array_shift($this->getImages()->toArray());
    }

    /**
     * Sets the Images
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $images
     * @return void
     */
    public function setImages($images)
    {
        $this->images = $images;
    }

    /**
     * Returns the Files
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $files
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Sets the Files
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\FileReference> $files
     * @return void
     */
    public function setFiles($files)
    {
        $this->files = $files;
    }

    /**
     * Returns the teaser
     *
     * @return string $teaser
     */
    public function getTeaser()
    {
        return $this->teaser;
    }

    /**
     * Sets the teaser
     *
     * @param string $teaser
     * @return void
     */
    public function setTeaser($teaser)
    {
        $this->teaser = $teaser;
    }

    /**
     * Returns the Product Content
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage
     */
    public function getProductContent()
    {
        return $this->productContent;
    }

    /**
     * Sets the Product Content
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $productContent
     * @return void
     */
    public function setProductContent($productContent)
    {
        $this->productContent = $productContent;
    }

    /**
     * @return int
     */
    public function getMinNumberInOrder()
    {
        return $this->minNumberInOrder;
    }

    /**
     * @param int $minNumberInOrder
     *
     * @return void
     */
    public function setMinNumberInOrder($minNumberInOrder)
    {
        if ($minNumberInOrder < 0 || $minNumberInOrder > $this->maxNumberInOrder) {
            throw new \InvalidArgumentException;
        }

        $this->minNumberInOrder = $minNumberInOrder;
    }

    /**
     * @return int
     */
    public function getMaxNumberInOrder()
    {
        return $this->maxNumberInOrder;
    }

    /**
     * @param int $maxNumberInOrder
     *
     * @return void
     */
    public function setMaxNumberInOrder($maxNumberInOrder)
    {
        if ($maxNumberInOrder < 0 || (($maxNumberInOrder != 0) && ($maxNumberInOrder < $this->minNumberInOrder))) {
            throw new \InvalidArgumentException;
        }

        $this->maxNumberInOrder = $maxNumberInOrder;
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
     * Adds a Special Price
     *
     * @param \Extcode\Cart\Domain\Model\Product\SpecialPrice $specialPrice
     * @return void
     */
    public function addSpecialPrice(\Extcode\Cart\Domain\Model\Product\SpecialPrice $specialPrice)
    {
        $this->specialPrices->attach($specialPrice);
    }

    /**
     * Removes a Special Price
     *
     * @param \Extcode\Cart\Domain\Model\Product\SpecialPrice $specialPriceToRemove
     * @return void
     */
    public function removeSpecialPrice(\Extcode\Cart\Domain\Model\Product\SpecialPrice $specialPriceToRemove)
    {
        $this->specialPrices->detach($specialPriceToRemove);
    }

    /**
     * Returns the Special Prices
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\specialPrice>
     */
    public function getSpecialPrices()
    {
        return $this->specialPrices;
    }

    /**
     * Sets the Special Prices
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $specialPrices
     */
    public function setSpecialPrices(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $specialPrices)
    {
        $this->specialPrices = $specialPrices;
    }

    /**
     * Returns best Special Price
     *
     * @return float
     */
    public function getBestSpecialPrice()
    {
        $bestSpecialPrice = $this->price;

        if ($this->specialPrices) {
            foreach ($this->specialPrices as $specialPrice) {
                if ($specialPrice->getPrice() < $bestSpecialPrice) {
                    $bestSpecialPrice = $specialPrice->getPrice();
                }
            }
        }

        return $bestSpecialPrice;
    }

    /**
     * Returns best Special Price Discount
     *
     * @return float
     */
    public function getBestSpecialPriceDiscount()
    {
        $bestSpecialPrice = $this->getBestSpecialPrice();
        $bestSpecialPriceDiscount = $this->price - $bestSpecialPrice;

        return $bestSpecialPriceDiscount;
    }

    /**
     * Returns best Special Price Percentage Discount
     *
     * @return float
     */
    public function getBestSpecialPricePercentageDiscount()
    {
        $estSpecialPricePercentageDiscount = (($this->getBestSpecialPriceDiscount()) / $this->price) * 100;

        return $estSpecialPricePercentageDiscount;
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
     * Returns the Base Price Measure Unit
     *
     * @return string $basePriceMeasureUnit
     */
    public function getBasePriceMeasureUnit()
    {
        return $this->basePriceMeasureUnit;
    }

    /**
     * Sets the Basse Price Measure Unit
     *
     * @param string $basePriceMeasureUnit
     *
     * @return void
     */
    public function setBasePriceMeasureUnit($basePriceMeasureUnit)
    {
        $this->basePriceMeasureUnit = $basePriceMeasureUnit;
    }

    /**
     * Check Measure Unit Compatibility
     *
     * @return bool
     */
    public function getIsMeasureUnitCompatibility()
    {
        foreach ($this->measureUnits as $measureUnit) {
            if (array_key_exists($this->basePriceMeasureUnit, $measureUnit)
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
     * @return float
     */
    public function getMeasureUnitFactor()
    {
        $factor = 1.0;

        foreach ($this->measureUnits as $measureUnit) {
            if ($measureUnit[$this->priceMeasureUnit]) {
                $factor = $factor / ($this->priceMeasure / $measureUnit[$this->priceMeasureUnit]);
            }
            if ($measureUnit[$this->basePriceMeasureUnit]) {
                $factor = $factor * (1.0 / $measureUnit[$this->basePriceMeasureUnit]);
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
            return $this->getMinPrice() * $this->getMeasureUnitFactor();
        }

        return false;
    }

    /**
     * Returns Service Attribute 1
     *
     * @return float
     */
    public function getServiceAttribute1()
    {
        return $this->serviceAttribute1;
    }

    /**
     * Sets Service Attribute 1
     *
     * @param float $serviceAttribute1
     *
     * @return void
     */
    public function setServiceAttribute1($serviceAttribute1)
    {
        $this->serviceAttribute1 = $serviceAttribute1;
    }

    /**
     * Returns Service Attribute 2
     *
     * @return float
     */
    public function getServiceAttribute2()
    {
        return $this->serviceAttribute2;
    }

    /**
     * Sets Service Attribute 2
     *
     * @param float $serviceAttribute2
     *
     * @return void
     */
    public function setServiceAttribute2($serviceAttribute2)
    {
        $this->serviceAttribute2 = $serviceAttribute2;
    }

    /**
     * Returns Service Attribute 3
     *
     * @return float
     */
    public function getServiceAttribute3()
    {
        return $this->serviceAttribute3;
    }

    /**
     * Sets Service Attribute 3
     *
     * @param float $serviceAttribute3
     *
     * @return void
     */
    public function setServiceAttribute3($serviceAttribute3)
    {
        $this->serviceAttribute3 = $serviceAttribute3;
    }

    /**
     * Returns Tax Class Id
     *
     * @return integer
     */
    public function getTaxClassId()
    {
        return $this->taxClassId;
    }

    /**
     * Sets Tax Class Id
     *
     * @param integer $taxClassId
     * @return void
     */
    public function setTaxClassId($taxClassId)
    {
        $this->taxClassId = $taxClassId;
    }

    /**
     * Returns the Variant Set 1
     *
     * @return \Extcode\Cart\Domain\Model\Product\BeVariantAttribute
     */
    public function getBeVariantAttribute1()
    {
        return $this->beVariantAttribute1;
    }

    /**
     * Sets the Variant Set 1
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariantAttribute $beVariantAttribute1
     * @return void
     */
    public function setBeVariantAttribute1(\Extcode\Cart\Domain\Model\Product\BeVariantAttribute $beVariantAttribute1)
    {
        $this->beVariantAttribute1 = $beVariantAttribute1;
    }

    /**
     * Returns the Variant Set 2
     *
     * @return \Extcode\Cart\Domain\Model\Product\BeVariantAttribute
     */
    public function getBeVariantAttribute2()
    {
        return $this->beVariantAttribute2;
    }

    /**
     * Sets the Variant Set 2
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariantAttribute $beVariantAttribute2
     * @return void
     */
    public function setBeVariantAttribute2(\Extcode\Cart\Domain\Model\Product\BeVariantAttribute $beVariantAttribute2)
    {
        $this->beVariantAttribute2 = $beVariantAttribute2;
    }

    /**
     * Returns the Variant Set 3
     *
     * @return \Extcode\Cart\Domain\Model\Product\BeVariantAttribute
     */
    public function getBeVariantAttribute3()
    {
        return $this->beVariantAttribute3;
    }

    /**
     * Sets the Variant Set 3
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariantAttribute $beVariantAttribute3
     * @return void
     */
    public function setBeVariantAttribute3(\Extcode\Cart\Domain\Model\Product\BeVariantAttribute $beVariantAttribute3)
    {
        $this->beVariantAttribute3 = $beVariantAttribute3;
    }

    /**
     * Adds a Variant
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariant $variant
     * @return void
     */
    public function addBeVariant(\Extcode\Cart\Domain\Model\Product\BeVariant $variant)
    {
        $this->beVariants->attach($variant);
    }

    /**
     * Removes a Variant
     *
     * @param \Extcode\Cart\Domain\Model\Product\BeVariant $variantToRemove
     * @return void
     */
    public function removeBeVariant(\Extcode\Cart\Domain\Model\Product\BeVariant $variantToRemove)
    {
        $this->beVariants->detach($variantToRemove);
    }

    /**
     * Returns the Variants
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\BeVariant> $variant
     */
    public function getBeVariants()
    {
        return $this->beVariants;
    }

    /**
     * Sets the Variants
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $variants
     */
    public function setBeVariants(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $variants)
    {
        $this->beVariants = $variants;
    }

    /**
     * Adds a Frontend Variant
     *
     * @param \Extcode\Cart\Domain\Model\Product\FeVariant $feVariant
     * @return void
     */
    public function addFeVariant(\Extcode\Cart\Domain\Model\Product\FeVariant $feVariant)
    {
        $this->feVariants->attach($feVariant);
    }

    /**
     * Removes a Frontend Variant
     *
     * @param \Extcode\Cart\Domain\Model\Product\FeVariant $feVariantToRemove
     * @return void
     */
    public function removeFeVariant(\Extcode\Cart\Domain\Model\Product\FeVariant $feVariantToRemove)
    {
        $this->feVariants->detach($feVariantToRemove);
    }

    /**
     * Returns the Frontend Variants
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\FeVariant> $variant
     */
    public function getFeVariants()
    {
        return $this->feVariants;
    }

    /**
     * Sets the Frontend Variants
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage $feVariants
     */
    public function setFeVariants(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $feVariants)
    {
        $this->feVariants = $feVariants;
    }

    /**
     * Adds a Related Product
     *
     * @param \Extcode\Cart\Domain\Model\Product\Product $relatedProduct
     * @return void
     */
    public function addRelatedProduct(\Extcode\Cart\Domain\Model\Product\Product $relatedProduct)
    {
        $this->relatedProducts->attach($relatedProduct);
    }

    /**
     * Removes a Related Product
     *
     * @param \Extcode\Cart\Domain\Model\Product\Product $relatedProductToRemove
     * @return void
     */
    public function removeRelatedProduct(\Extcode\Cart\Domain\Model\Product\Product $relatedProductToRemove)
    {
        $this->relatedProducts->detach($relatedProductToRemove);
    }

    /**
     * Returns the Related Products
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\Product> $relatedProduct
     */
    public function getRelatedProducts()
    {
        return $this->relatedProducts;
    }

    /**
     * Sets the Related Products
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\Product> $relatedProducts
     */
    public function setRelatedProducts(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $relatedProducts)
    {
        $this->relatedProducts = $relatedProducts;
    }

    /**
     * Adds a Related Product (from)
     *
     * @param \Extcode\Cart\Domain\Model\Product\Product $relatedProductFrom
     * @return void
     */
    public function addRelatedProductFrom(\Extcode\Cart\Domain\Model\Product\Product $relatedProductFrom)
    {
        $this->relatedProductsFrom->attach($relatedProductFrom);
    }

    /**
     * Removes a Related Product (from)
     *
     * @param \Extcode\Cart\Domain\Model\Product\Product $relatedProductFromToRemove
     * @return void
     */
    public function removeRelatedProductFrom(\Extcode\Cart\Domain\Model\Product\Product $relatedProductFromToRemove)
    {
        $this->relatedProductsFrom->detach($relatedProductFromToRemove);
    }

    /**
     * Returns the Related Products (from)
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\Product> $relatedProductFrom
     */
    public function getRelatedProductsFrom()
    {
        return $this->relatedProductsFrom;
    }

    /**
     * Sets the Related Products (from)
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\Product> $relatedProductsFrom
     */
    public function setRelatedProductsFrom(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $relatedProductsFrom)
    {
        $this->relatedProductsFrom = $relatedProductsFrom;
    }

    /**
     * Returns the Stock
     *
     * @return int
     */
    public function getStock()
    {
        if (count($this->beVariants)) {
            $count = 0;

            foreach ($this->beVariants as $variant) {
                $count += $variant->getStock();
            }

            return $count;
        }
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
     * Add To Stock
     *
     * @param int $numberOfProducts
     */
    public function addToStock($numberOfProducts)
    {
        if ($this->handleStock()) {
            $this->stock += $numberOfProducts;
        }
    }

    /**
     * Remove From Stock
     *
     * @param int $numberOfProducts
     */
    public function removeFromStock($numberOfProducts)
    {
        if ($this->handleStock()) {
            $this->stock -= $numberOfProducts;
        }
    }

    /**
     * @return bool
     */
    public function handleStock()
    {
        return $this->handleStock;
    }

    /**
     * Sets Handle Stock
     *
     * @param bool $handleStock
     * @return void
     */
    public function setHandleStock($handleStock)
    {
        $this->handleStock = $handleStock;
    }

    /**
     * Adds a Product Category
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $Category
     * @return void
     */
    public function addCategory(\TYPO3\CMS\Extbase\Domain\Model\Category $category)
    {
        $this->categories->attach($category);
    }

    /**
     * Removes a Category
     *
     * @param \TYPO3\CMS\Extbase\Domain\Model\Category $categoryToRemove
     * @return void
     */
    public function removeCategory(\TYPO3\CMS\Extbase\Domain\Model\Category $categoryToRemove)
    {
        $this->categories->detach($categoryToRemove);
    }

    /**
     * Returns the categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Category> $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Returns the first category
     *
     * @return \Extcode\Cart\Domain\Model\Category
     */
    public function getFirstCategory()
    {
        $categories = $this->getCategories();
        if (!is_null($categories)) {
            $categories->rewind();
            return $categories->current();
        } else {
            return null;
        }
    }

    /**
     * Sets the categories
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\TYPO3\CMS\Extbase\Domain\Model\Category> $categories
     */
    public function setCategories(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories)
    {
        $this->categories = $categories;
    }

    /**
     * get the minimal Price from Variants
     *
     * @return float
     */
    public function getMinPrice()
    {
        $minPrice = null;
        if (count($this->getBeVariants())) {
            foreach ($this->getBeVariants() as $variant) {
                if (!isset($minPrice)) {
                    $minPrice = $variant->getBestPriceCalculated();
                } else {
                    if ($variant->getBestPriceCalculated() < $minPrice) {
                        $minPrice = $variant->getBestPriceCalculated();
                    }
                }
            }
        } else {
            $minPrice = $this->getPrice();
        }

        return $minPrice;
    }

    /**
     * Returns MeasureUnits
     *
     * @return array
     */
    public function getMeasureUnits()
    {
        return $this->measureUnits;
    }

    /**
     * Sets MeasureUnits
     *
     * @param array $measureUnits
     *
     * @return void
     */
    public function setMeasureUnits($measureUnits)
    {
        $this->measureUnits = $measureUnits;
    }

    /**
     * Adds a Tag
     *
     * @param \Extcode\Cart\Domain\Model\Product\Tag $tag
     * @return void
     */
    public function addTag(\Extcode\Cart\Domain\Model\Product\Tag $tag)
    {
        $this->tags->attach($tag);
    }

    /**
     * Removes a Tag
     *
     * @param \Extcode\Cart\Domain\Model\Product\Tag $tagToRemove
     * @return void
     */
    public function removeTag(\Extcode\Cart\Domain\Model\Product\Tag $tagToRemove)
    {
        $this->tags->detach($tagToRemove);
    }

    /**
     * Returns the Tags
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\Tag>
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Sets the Tags
     *
     * @param  \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Product\Tag> $tags
     */
    public function setTags(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $tags)
    {
        $this->tags = $tags;
    }
}
