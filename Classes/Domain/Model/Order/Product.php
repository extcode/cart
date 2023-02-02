<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Annotation\Validate;
use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
use TYPO3\CMS\Extbase\Persistence\ObjectStorage;

class Product extends AbstractEntity
{
    protected Item $item;

    protected int $productId = 0;

    protected string $productType = '';

    /**
     * @Validate("NotEmpty")
     */
    protected string $sku = '';

    /**
     * @Validate("NotEmpty")
     */
    protected string $title = '';

    /**
     * @Validate("NotEmpty")
     */
    protected int $count = 0;

    /**
     * @Validate("NotEmpty")
     */
    protected float $price = 0.0;

    /**
     * @Validate("NotEmpty")
     */
    protected float $discount = 0.0;

    /**
     * @Validate("NotEmpty")
     */
    protected float $gross = 0.0;

    /**
     * @Validate("NotEmpty")
     */
    protected float $net = 0.0;

    /**
     * @Validate("NotEmpty")
     */
    protected TaxClass $taxClass;

    /**
     * @Validate("NotEmpty")
     */
    protected float $tax = 0.0;

    protected string $additionalData = '';

    /**
     * @var ObjectStorage<ProductAdditional>
     */
    protected ObjectStorage $productAdditional;

    protected string $additional = '';

    public function __construct()
    {
        $this->initStorageObjects();
    }

    protected function initStorageObjects(): void
    {
        $this->productAdditional = new ObjectStorage();
    }

    public function getItem(): ?Item
    {
        return $this->item;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function setProductId(int $productId): void
    {
        $this->productId = $productId;
    }

    public function getProductType(): string
    {
        return $this->productType;
    }

    public function setProductType(string $productType): void
    {
        $this->productType = $productType;
    }

    public function getAdditionalData(): string
    {
        return $this->additionalData;
    }

    public function setAdditionalData(string $additionalData): void
    {
        $this->additionalData = $additionalData;
    }

    public function getCount(): int
    {
        return $this->count;
    }

    public function setCount(int $count): void
    {
        $this->count = $count;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setDiscount(float $discount): void
    {
        $this->discount = $discount;
    }

    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function setGross(float $gross): void
    {
        $this->gross = $gross;
    }

    public function getGross(): float
    {
        return $this->gross;
    }

    public function setNet(float $net): void
    {
        $this->net = $net;
    }

    public function getNet(): float
    {
        return $this->net;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getTaxClass(): ?TaxClass
    {
        return $this->taxClass;
    }

    public function setTaxClass(TaxClass $taxClass): void
    {
        $this->taxClass = $taxClass;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function setTax(float $tax): void
    {
        $this->tax = $tax;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function addProductAdditional(ProductAdditional $productAdditional): void
    {
        $this->productAdditional->attach($productAdditional);
    }

    public function removeProductAdditional(ProductAdditional $productAdditional): void
    {
        $this->productAdditional->detach($productAdditional);
    }

    /**
     * @return ObjectStorage<ProductAdditional>
     */
    public function getProductAdditional(): ObjectStorage
    {
        return $this->productAdditional;
    }

    /**
     * @param ObjectStorage<ProductAdditional> $productAdditional
     */
    public function setProductAdditional(ObjectStorage $productAdditional): void
    {
        $this->productAdditional = $productAdditional;
    }

    public function getAdditional(): array
    {
        if ($this->additional) {
            return json_decode($this->additional, true);
        }

        return [];
    }

    public function setAdditional(array $additional): void
    {
        $this->additional = json_encode($additional);
    }
}
