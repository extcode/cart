<?php

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Product extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var \Extcode\Cart\Domain\Model\Order\Item
     */
    protected $item;

    /**
     * @var int
     */
    protected $productId = 0;

    /**
     * @var string
     */
    protected $productType = '';

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $sku = '';

    /**
     * @var string
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $title = '';

    /**
     * @var int
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $count = 0;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $price = 0.0;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $discount = 0.0;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $gross = 0.0;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $net = 0.0;

    /**
     * @var \Extcode\Cart\Domain\Model\Order\TaxClass
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $taxClass;

    /**
     * @var float
     * @TYPO3\CMS\Extbase\Annotation\Validate("NotEmpty")
     */
    protected $tax = 0.0;

    /**
     * @var string
     */
    protected $additionalData = '';

    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\ProductAdditional>
     */
    protected $productAdditional;

    /**
     * @var string
     */
    protected $additional = '';

    /**
     * @param string $sku
     * @param string $title
     * @param int $count
     */
    public function __construct(
        string $sku,
        string $title,
        int $count
    ) {
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
     * @return Item|null
     */
    public function getItem(): ?Item
    {
        return $this->item;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @param int $productId
     */
    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    /**
     * @return string
     */
    public function getProductType(): string
    {
        return $this->productType;
    }

    /**
     * @var string $productType
     */
    public function setProductType(string $productType)
    {
        $this->productType = $productType;
    }

    /**
     * @return string
     */
    public function getAdditionalData(): string
    {
        return $this->additionalData;
    }

    /**
     * @param string $additionalData
     */
    public function setAdditionalData(string $additionalData)
    {
        $this->additionalData = $additionalData;
    }

    /**
     * @return int
     */
    public function getCount(): int
    {
        return $this->count;
    }

    /**
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @param float $discount
     */
    public function setDiscount(float $discount)
    {
        $this->discount = $discount;
    }

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        return $this->discount;
    }

    /**
     * @param float $gross
     */
    public function setGross(float $gross)
    {
        $this->gross = $gross;
    }

    /**
     * @return float
     */
    public function getGross(): float
    {
        return $this->gross;
    }

    /**
     * @param float $net
     */
    public function setNet(float $net)
    {
        $this->net = $net;
    }

    /**
     * @return float
     */
    public function getNet(): float
    {
        return $this->net;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @return TaxClass|null
     */
    public function getTaxClass(): ?TaxClass
    {
        return $this->taxClass;
    }

    /**
     * @param TaxClass $taxClass
     */
    public function setTaxClass(TaxClass $taxClass)
    {
        $this->taxClass = $taxClass;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * @param float $tax
     */
    public function setTax(float $tax)
    {
        $this->tax = $tax;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param ProductAdditional $productAdditional
     */
    public function addProductAdditional(ProductAdditional $productAdditional)
    {
        $this->productAdditional->attach($productAdditional);
    }

    /**
     * @param ProductAdditional $productAdditional
     */
    public function removeProductAdditional(ProductAdditional $productAdditional)
    {
        $this->productAdditional->detach($productAdditional);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\ProductAdditional>
     */
    public function getProductAdditional()
    {
        return $this->productAdditional;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Extcode\Cart\Domain\Model\Order\ProductAdditional> $productAdditional
     */
    public function setProductAdditional(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $productAdditional)
    {
        $this->productAdditional = $productAdditional;
    }

    /**
     * @return array
     */
    public function getAdditional(): array
    {
        if ($this->additional) {
            return json_decode($this->additional, true);
        }

        return [];
    }

    /**
     * @param array $additional
     */
    public function setAdditional(array $additional)
    {
        $this->additional = json_encode($additional);
    }
}
