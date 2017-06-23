<?php

namespace Extcode\Cart\Domain\Model\Order;

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
 * Order Product Model
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class Product extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * Item
     *
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $item;

    /**
     * Product Type
     *
     * @var string
     */
    protected $productType = '';

    /**
     * Sku
     *
     * @var string
     * @validate NotEmpty
     */
    protected $sku;

    /**
     * Title
     *
     * @var string
     * @validate NotEmpty
     */
    protected $title;

    /**
     * Count
     *
     * @var int
     * @validate NotEmpty
     */
    protected $count = 0;

    /**
     * Price
     *
     * @var float
     * @validate NotEmpty
     */
    protected $price = 0.0;

    /**
     * Discount
     *
     * @var float
     * @validate NotEmpty
     */
    protected $discount = 0.0;

    /**
     * Gross
     *
     * @var float
     * @validate NotEmpty
     */
    protected $gross = 0.0;

    /**
     * Gross
     *
     * @var float
     * @validate NotEmpty
     */
    protected $net = 0.0;

    /**
     * Order Tax Class
     *
     * @var \Extcode\Cart\Domain\Model\Order\TaxClass
     * @validate NotEmpty
     */
    protected $taxClass;

    /**
     * Tax
     *
     * @var float
     * @validate NotEmpty
     */
    protected $tax = 0.0;

    /**
     * Additional Data
     *
     * @var string
     */
    protected $additionalData;

    /**
     * Product Additional
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<Extcode\Cart\Domain\Model\Order\ProductAdditional>
     */
    protected $productAdditional;

    /**
     * Additional
     *
     * @var string
     */
    protected $additional;

    /**
     * __construct
     *
     * @param string $sku
     * @param string $title
     * @param int $count
     *
     * @return \Extcode\Cart\Domain\Model\Order\Product
     */
    public function __construct(
        $sku,
        $title,
        $count
    ) {
        if (!$sku) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $sku for constructor.',
                1456830010
            );
        }
        if (!$title) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $title for constructor.',
                1456830020
            );
        }
        if (!$count) {
            throw new \InvalidArgumentException(
                'You have to specify a valid $count for constructor.',
                1456830030
            );
        }

        $this->sku = $sku;
        $this->title = $title;
        $this->count = $count;

        $this->initStorageObjects();
    }

    /**
     * Initializes all \TYPO3\CMS\Extbase\Persistence\ObjectStorage properties.
     */
    protected function initStorageObjects()
    {
        $this->productAdditional = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Returns the Order Item
     *
     * @return \Extcode\Cart\Domain\Model\Order\Item
     */
    public function getItem()
    {
        return $this->item;
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
     * Sets the Product Type
     *
     * @var string $productType
     */
    public function setProductType($productType)
    {
        $this->productType = $productType;
    }

    /**
     * Gets Additional Data
     *
     * @return string
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * Sets Additional Data
     *
     * @param string $additionalData
     */
    public function setAdditionalData($additionalData)
    {
        $this->additionalData = $additionalData;
    }

    /**
     * Gets Count
     *
     * @return int
     */
    public function getCount()
    {
        return $this->count;
    }

    /**
     * Sets Price
     *
     * @param float $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
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
     * Sets Discount
     *
     * @param float $discount
     */
    public function setDiscount($discount)
    {
        $this->discount = $discount;
    }

    /**
     * Gets Discount
     *
     * @return float
     */
    public function getDiscount()
    {
        return $this->discount;
    }

    /**
     * Sets Gross
     *
     * @param float $gross
     */
    public function setGross($gross)
    {
        $this->gross = $gross;
    }

    /**
     * Gets Gross
     *
     * @return float
     */
    public function getGross()
    {
        return $this->gross;
    }

    /**
     * Sets Net
     *
     * @param float $net
     */
    public function setNet($net)
    {
        $this->net = $net;
    }

    /**
     * Gets Net
     *
     * @return float
     */
    public function getNet()
    {
        return $this->net;
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
     * Gets Tax Class
     *
     * @return \Extcode\Cart\Domain\Model\Order\TaxClass
     */
    public function getTaxClass()
    {
        return $this->taxClass;
    }

    /**
     * Sets Tax Class
     *
     * @param \Extcode\Cart\Domain\Model\Order\TaxClass $taxClass
     */
    public function setTaxClass(\Extcode\Cart\Domain\Model\Order\TaxClass $taxClass)
    {
        $this->taxClass = $taxClass;
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
     * Sets Tax
     *
     * @param float $tax
     */
    public function setTax($tax)
    {
        $this->tax = $tax;
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
     * Adds a ProductAdditional
     *
     * @param \Extcode\Cart\Domain\Model\Order\ProductAdditional $productAdditional
     */
    public function addProductAdditional(\Extcode\Cart\Domain\Model\Order\ProductAdditional $productAdditional)
    {
        $this->productAdditional->attach($productAdditional);
    }

    /**
     * Removes a ProductAdditional
     *
     * @param \Extcode\Cart\Domain\Model\Order\ProductAdditional $productAdditional
     */
    public function removeProductAdditional(\Extcode\Cart\Domain\Model\Order\ProductAdditional $productAdditional)
    {
        $this->productAdditional->detach($productAdditional);
    }

    /**
     * Returns the ProductAdditional
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\ProductAdditional>
     */
    public function getProductAdditional()
    {
        return $this->productAdditional;
    }

    /**
     * Sets the ProductAdditional
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\ProductAdditional> $productAdditional
     */
    public function setProductAdditional(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $productAdditional)
    {
        $this->productAdditional = $productAdditional;
    }

    /**
     * @return array
     */
    public function getAdditional()
    {
        return json_decode($this->additional, 1);
    }

    /**
     * @return string
     */
    public function getAdditionalJson()
    {
        return $this->additional;
    }

    /**
     * @param array $additional
     */
    public function setAdditional($additional)
    {
        $this->additional = json_encode($additional);
    }
}
