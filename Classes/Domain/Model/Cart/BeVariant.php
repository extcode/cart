<?php

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
    /**
     * @var string
     */
    protected $id = '';

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\Product
     */
    protected $product = null;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\BeVariant
     */
    protected $parentBeVariant = null;

    /**
     * @var string
     */
    protected $title = '';

    /**
     * @var string
     */
    protected $titleDelimiter = ' - ';

    /**
     * @var string
     */
    protected $sku = '';

    /**
     * @var string
     */
    protected $skuDelimiter = '-';

    /**
     * @var int
     */
    protected $priceCalcMethod = 0;

    /**
     * @var float
     */
    protected $price = 0.0;

    /**
     * @var float
     */
    protected $specialPrice;

    /**
     * @var int
     */
    protected $quantity = 0;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\BeVariant[]
     */
    protected $beVariants = [];

    /**
     * @var float
     */
    protected $gross = 0.0;

    /**
     * @var float
     */
    protected $net = 0.0;

    /**
     * @var float
     */
    protected $tax = 0.0;

    /**
     * @var bool
     */
    protected $isFeVariant = false;

    /**
     * @var int
     */
    protected $hasFeVariants;

    /**
     * @var int
     */
    protected $min = 0;

    /**
     * @var int
     */
    protected $max = 0;

    /**
     * @var array Additional
     */
    protected $additional = [];

    /**
     * @var int
     */
    protected $stock = 0;

    /**
     * @param string $id
     * @param Product $product
     * @param BeVariant $beVariant
     * @param string $title
     * @param string $sku
     * @param int $priceCalcMethod
     * @param float $price
     * @param int $quantity
     */
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
            throw new \InvalidArgumentException;
        }

        if ($product != null && $beVariant != null) {
            throw new \InvalidArgumentException;
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
     * @return Product|null
     */
    public function getProduct(): ?Product
    {
        return $this->product;
    }

    /**
     * @var Product $product
     */
    public function setProduct(Product $product)
    {
        $this->product = $product;
    }

    /**
     * @return BeVariant
     */
    public function getParentBeVariant(): ?self
    {
        return $this->parentBeVariant;
    }

    /**
     * @var BeVariant $parentBeVariant
     */
    public function setParentBeVariant(self $parentBeVariant)
    {
        $this->parentBeVariant = $parentBeVariant;
    }

    /**
     * @return bool
     */
    public function getIsNetPrice(): bool
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
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getTitleDelimiter(): string
    {
        return $this->titleDelimiter;
    }

    /**
     * @param string $titleDelimiter
     */
    public function setTitleDelimiter(string $titleDelimiter)
    {
        $this->titleDelimiter = $titleDelimiter;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
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

    /**
     * Gets CompleteTitleWithoutProduct
     *
     * @return string
     */
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

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float|null
     */
    public function getSpecialPrice(): ?float
    {
        return $this->specialPrice;
    }

    /**
     * @param float $specialPrice
     */
    public function setSpecialPrice(float $specialPrice)
    {
        $this->specialPrice = $specialPrice;
    }

    /**
     * Returns Best Price (min of Price and Special Price)
     *
     * @return float
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

    /**
     * @return float
     */
    public function getDiscount(): float
    {
        $discount = $this->getPriceCalculated() - $this->getBestPriceCalculated();

        return $discount;
    }

    /**
     * @return float
     */
    public function getSpecialPriceDiscount(): float
    {
        $discount = 0.0;
        if (($this->price != 0.0) && ($this->specialPrice)) {
            $discount = (($this->price - $this->specialPrice) / $this->price) * 100;
        }
        return $discount;
    }

    /**
     * @return float
     */
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
     * @return float
     */
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
     * @return float
     */
    public function getParentPrice(): float
    {
        if ($this->priceCalcMethod == 1) {
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

    /**
     * @param float $price
     */
    public function setPrice(float $price)
    {
        $this->price = $price;

        $this->reCalc();
    }

    /**
     * @return int
     */
    public function getPriceCalcMethod(): int
    {
        return $this->priceCalcMethod;
    }

    /**
     * @param int $priceCalcMethod
     */
    public function setPriceCalcMethod(int $priceCalcMethod)
    {
        $this->priceCalcMethod = $priceCalcMethod;
    }

    /**
     * @return string
     */
    public function getSkuDelimiter(): string
    {
        return $this->skuDelimiter;
    }

    /**
     * @param string $skuDelimiter
     */
    public function setSkuDelimiter(string $skuDelimiter)
    {
        $this->skuDelimiter = $skuDelimiter;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @return string
     */
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

    /**
     * @return string
     */
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

    /**
     * @param string $sku
     */
    public function setSku(string $sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return int
     */
    public function getHasFeVariants(): int
    {
        return $this->hasFeVariants;
    }

    /**
     * @param int $hasFeVariants
     */
    public function setHasFeVariants(int $hasFeVariants)
    {
        $this->hasFeVariants = $hasFeVariants;
    }

    /**
     * @return bool
     */
    public function getIsFeVariant(): bool
    {
        return $this->isFeVariant;
    }

    /**
     * @param bool $isFeVariant
     */
    public function setIsFeVariant(bool $isFeVariant)
    {
        $this->isFeVariant = $isFeVariant;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return float
     */
    public function getGross(): float
    {
        $this->calcGross();
        return $this->gross;
    }

    /**
     * @return float
     */
    public function getNet(): float
    {
        $this->calcNet();
        return $this->net;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        return $this->tax;
    }

    /**
     * @return TaxClass
     */
    public function getTaxClass() :TaxClass
    {
        if ($this->getParentBeVariant()) {
            $taxClass = $this->getParentBeVariant()->getTaxClass();
        } elseif ($this->getProduct()) {
            $taxClass = $this->getProduct()->getTaxClass();
        }
        return $taxClass;
    }

    /**
     * @param int $newQuantity
     */
    public function setQuantity(int $newQuantity)
    {
        $this->quantity = $newQuantity;

        $this->reCalc();
    }

    /**
     * @param int $newQuantity
     */
    public function changeQuantity(int $newQuantity)
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
     * @param array $variantQuantityArray
     * @internal param $id
     * @internal param $newQuantity
     */
    public function changeVariantsQuantity(array $variantQuantityArray)
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
    public function addBeVariants(array $newVariants)
    {
        foreach ($newVariants as $newVariant) {
            $this->addBeVariant($newVariant);
        }
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $newBeVariant
     */
    public function addBeVariant(self $newBeVariant)
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
    public function getBeVariants(): array
    {
        return $this->beVariants;
    }

    /**
     * @param int $beVariantId
     *
     * @return BeVariant|null
     */
    public function getBeVariantById(int $beVariantId): ?self
    {
        return $this->beVariants[$beVariantId];
    }

    /**
     * @param int $beVariantId
     *
     * @return BeVariant|null
     */
    public function getBeVariant(int $beVariantId): ?self
    {
        return $this->getBeVariantById($beVariantId);
    }

    /**
     * @param array $beVariantsArray
     * @return bool|int
     */
    public function removeBeVariants(array $beVariantsArray)
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
    public function getAdditionalArray(): array
    {
        return $this->additional;
    }

    /**
     * @param array $additional
     */
    public function setAdditionalArray(array $additional)
    {
        $this->additional = $additional;
    }

    /**
     * @param string $key
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
    public function setAdditional($key, $value)
    {
        $this->additional[$key] = $value;
    }

    /**
     * @return int
     */
    public function getMin(): int
    {
        return $this->min;
    }

    /**
     * @param int $min
     */
    public function setMin(int $min)
    {
        if ($min < 0 || $min > $this->max) {
            throw new \InvalidArgumentException;
        }

        $this->min = $min;
    }

    /**
     * @return int
     */
    public function getMax(): int
    {
        return $this->max;
    }

    /**
     * @param int $max
     */
    public function setMax(int $max)
    {
        if ($max < 0 || $max < $this->min) {
            throw new \InvalidArgumentException;
        }

        $this->max = $max;
    }

    /**
     * @return int
     */
    public function getStock(): int
    {
        return $this->stock;
    }

    /**
     * @param int $stock
     */
    public function setStock(int $stock)
    {
        $this->stock = $stock;
    }
}
