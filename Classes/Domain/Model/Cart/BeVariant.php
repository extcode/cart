<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

class BeVariant
{
    protected string $id = '';

    protected ?Product $product = null;

    protected ?BeVariant $parentBeVariant = null;

    protected string $title = '';

    protected string $titleDelimiter = ' - ';

    protected string $sku = '';

    protected string $skuDelimiter = '-';

    protected int $priceCalcMethod = 0;

    protected float $price = 0.0;

    protected ?float $specialPrice = null;

    protected int $quantity = 0;

    protected array $beVariants = [];

    protected float $gross = 0.0;

    protected float $net = 0.0;

    protected float $tax = 0.0;

    protected bool $isFeVariant = false;

    protected int $hasFeVariants;

    protected int $min = 0;

    protected int $max = 0;

    protected array $additional = [];

    protected int $stock = 0;

    public function __construct(
        string $id,
        Product $product = null,
        self $beVariant = null,
        string $title,
        string $sku,
        int $priceCalcMethod,
        float $price,
        int $quantity = 0
    ) {
        if ($product === null && $beVariant === null) {
            throw new \InvalidArgumentException();
        }

        if ($product != null && $beVariant != null) {
            throw new \InvalidArgumentException();
        }

        $this->id = $id;

        if ($product !== null) {
            $this->product = $product;
        }

        if ($beVariant !== null) {
            $this->parentBeVariant = $beVariant;
        }

        $this->title = $title;
        $this->sku = $sku;
        $this->priceCalcMethod = $priceCalcMethod;
        $this->price = $price;
        $this->quantity = $quantity;

        $this->reCalc();
    }

    public function toArray(): array
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
            'additional' => $this->additional,
        ];

        if ($this->beVariants) {
            $innerVariantArr = [];

            foreach ($this->beVariants as $variant) {
                /** @var BeVariant $variant */
                $innerVariantArr[] = [$variant->getId() => $variant->toArray()];
            }

            $variantArr[] = ['variants' => $innerVariantArr];
        }

        return $variantArr;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    public function getParentBeVariant(): ?self
    {
        return $this->parentBeVariant;
    }

    public function setParentBeVariant(self $parentBeVariant): void
    {
        $this->parentBeVariant = $parentBeVariant;
    }

    public function isNetPrice(): bool
    {
        $isNetPrice = false;

        if ($this->getParentBeVariant()) {
            $isNetPrice = $this->getParentBeVariant()->isNetPrice();
        } elseif ($this->getProduct()) {
            $isNetPrice = $this->getProduct()->isNetPrice();
        }

        return $isNetPrice;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitleDelimiter(): string
    {
        return $this->titleDelimiter;
    }

    public function setTitleDelimiter(string $titleDelimiter): void
    {
        $this->titleDelimiter = $titleDelimiter;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getCompleteTitle(): string
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

    public function getCompleteTitleWithoutProduct(): string
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

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getSpecialPrice(): ?float
    {
        return $this->specialPrice;
    }

    public function setSpecialPrice(float $specialPrice): void
    {
        $this->specialPrice = $specialPrice;
    }

    /**
     * Returns Best Price (min of Price and Special Price)
     */
    public function getBestPrice(): float
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

    public function getDiscount(): float
    {
        $discount = $this->getPriceCalculated() - $this->getBestPriceCalculated();

        return $discount;
    }

    public function getSpecialPriceDiscount(): float
    {
        $discount = 0.0;
        if (($this->price != 0.0) && ($this->specialPrice)) {
            $discount = (($this->price - $this->specialPrice) / $this->price) * 100;
        }
        return $discount;
    }

    public function getPriceCalculated(): float
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

        if (
            isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['changeVariantDiscount']) &&
            is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['changeVariantDiscount'])
        ) {
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

    public function getBestPriceCalculated(): float
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

        if (
            isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['changeVariantDiscount']) &&
            is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['changeVariantDiscount'])
        ) {
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

    public function getParentPrice(): float
    {
        if ($this->priceCalcMethod === 1) {
            return 0.0;
        }

        if ($this->getParentBeVariant()) {
            return $this->getParentBeVariant()->getBestPrice();
        }

        if ($this->getProduct()) {
            return $this->getProduct()->getBestPrice($this->getQuantity());
        }

        return 0.0;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;

        $this->reCalc();
    }

    public function getPriceCalcMethod(): int
    {
        return $this->priceCalcMethod;
    }

    public function setPriceCalcMethod(int $priceCalcMethod): void
    {
        $this->priceCalcMethod = $priceCalcMethod;
    }

    public function getSkuDelimiter(): string
    {
        return $this->skuDelimiter;
    }

    public function setSkuDelimiter(string $skuDelimiter): void
    {
        $this->skuDelimiter = $skuDelimiter;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function getCompleteSku(): string
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

    public function getCompleteSkuWithoutProduct(): string
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

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getHasFeVariants(): int
    {
        return $this->hasFeVariants;
    }

    public function setHasFeVariants(int $hasFeVariants): void
    {
        $this->hasFeVariants = $hasFeVariants;
    }

    public function isFeVariant(): bool
    {
        return $this->isFeVariant;
    }

    public function setIsFeVariant(bool $isFeVariant): void
    {
        $this->isFeVariant = $isFeVariant;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getGross(): float
    {
        $this->calcGross();
        return $this->gross;
    }

    public function getNet(): float
    {
        $this->calcNet();
        return $this->net;
    }

    public function getTax(): float
    {
        return $this->tax;
    }

    public function getTaxClass(): ?TaxClass
    {
        if ($this->getParentBeVariant()) {
            return $this->getParentBeVariant()->getTaxClass();
        }
        if ($this->getProduct()) {
            return $this->getProduct()->getTaxClass();
        }

        return null;
    }

    public function setQuantity(int $newQuantity): void
    {
        $this->quantity = $newQuantity;

        $this->reCalc();
    }

    public function changeQuantity(int $newQuantity): void
    {
        $this->quantity = $newQuantity;

        if ($this->beVariants) {
            foreach ($this->beVariants as $beVariant) {
                $beVariant->changeQuantity($newQuantity);
            }
        }

        $this->reCalc();
    }

    public function changeVariantsQuantity(array $variantQuantityArray): void
    {
        foreach ($variantQuantityArray as $beVariantId => $quantity) {
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

    public function addBeVariants(array $newVariants): void
    {
        foreach ($newVariants as $newVariant) {
            $this->addBeVariant($newVariant);
        }
    }

    public function addBeVariant(self $newBeVariant): void
    {
        $newBeVariantId = $newBeVariant->getId();

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

    public function getBeVariants(): array
    {
        return $this->beVariants;
    }

    public function getBeVariantById(int $beVariantId): ?self
    {
        if (isset($this->beVariants[$beVariantId])) {
            return $this->beVariants[$beVariantId];
        }

        return null;
    }

    /**
     * @return bool|int
     */
    public function removeBeVariants(array $beVariantsArray)
    {
        foreach ($beVariantsArray as $beVariantId => $value) {
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

    protected function calcGross(): void
    {
        if ($this->isNetPrice() === false) {
            if ($this->beVariants) {
                $sum = 0.0;
                foreach ($this->beVariants as $beVariant) {
                    /** @var BeVariant $beVariant */
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

    protected function calcTax(): void
    {
        if ($this->isNetPrice() === false) {
            $this->calcGross();
            $this->tax = ($this->gross / (1 + $this->getTaxClass()->getCalc())) * ($this->getTaxClass()->getCalc());
        } else {
            $this->calcNet();
            $this->tax = ($this->net * $this->getTaxClass()->getCalc());
        }
    }

    protected function calcNet(): void
    {
        if ($this->isNetPrice() === true) {
            if ($this->beVariants) {
                $sum = 0.0;
                foreach ($this->beVariants as $beVariant) {
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

    protected function reCalc(): void
    {
        if ($this->beVariants) {
            $quantity = 0;
            foreach ($this->beVariants as $beVariant) {
                $quantity += $beVariant->getQuantity();
            }

            if ($this->quantity != $quantity) {
                $this->quantity = $quantity;
            }
        }

        if ($this->isNetPrice() === false) {
            $this->calcGross();
            $this->calcTax();
            $this->calcNet();
        } else {
            $this->calcNet();
            $this->calcTax();
            $this->calcGross();
        }
    }

    public function getAdditionalArray(): array
    {
        return $this->additional;
    }

    public function setAdditionalArray(array $additional): void
    {
        $this->additional = $additional;
    }

    /**
     * @return mixed
     */
    public function getAdditional(string $key)
    {
        return $this->additional[$key];
    }

    /**
     * @param string $key
     * @param mixed $value
     */
    public function setAdditional($key, $value): void
    {
        $this->additional[$key] = $value;
    }

    public function getMin(): int
    {
        return $this->min;
    }

    public function setMin(int $min): void
    {
        if ($min < 0 || $min > $this->max) {
            throw new \InvalidArgumentException();
        }

        $this->min = $min;
    }

    public function getMax(): int
    {
        return $this->max;
    }

    public function setMax(int $max): void
    {
        if ($max < 0 || $max < $this->min) {
            throw new \InvalidArgumentException();
        }

        $this->max = $max;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }
}
