<?php

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Product
{
    /**
     * @var string
     */
    protected $productType;

    /**
     * @var int
     */
    protected $productId;

    /**
     * @var Cart
     */
    protected $cart = null;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $sku;

    /**
     * @var float
     */
    protected $price;

    /**
     * @var float
     */
    protected $specialPrice = null;

    /**
     * @var array
     */
    protected $quantityDiscounts = [];

    /**
     * @var int
     */
    protected $quantity;

    /**
     * @var float
     */
    protected $gross;

    /**
     * @var float
     */
    protected $net;

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\TaxClass
     */
    protected $taxClass;

    /**
     * @var float
     */
    protected $tax;

    /**
     * @var string
     */
    protected $error;

    /**
     * @var bool
     */
    protected $isVirtualProduct = false;

    /**
     * @var float
     */
    protected $serviceAttribute1;

    /**
     * @var float
     */
    protected $serviceAttribute2;

    /**
     * @var float
     */
    protected $serviceAttribute3;

    /**
     * @var bool
     */
    protected $isNetPrice = false;

    /**
     * @var array BeVariant
     */
    protected $beVariants = [];

    /**
     * @var \Extcode\Cart\Domain\Model\Cart\FeVariant
     */
    protected $feVariant = null;

    /**
     * @var array Additional
     */
    protected $additional = [];

    /**
     * @var int
     */
    protected $minNumberInCart = 0;

    /**
     * @var int
     */
    protected $maxNumberInCart = 0;

    /**
     * Number of products in stock.
     *
     * @var int
     */
    protected $stock = 0;

    /**
     * Is the stock management active for this product?
     *
     * @var bool
     */
    protected $handleStock = false;

    /**
     * Is the stock management for this product covered in the backend variants?
     *
     * @var bool
     */
    protected $handleStockInVariants = false;

    /**
     * @param string $productType
     * @param int $productId
     * @param string $sku
     * @param string $title
     * @param float $price
     * @param TaxClass $taxClass
     * @param int $quantity
     * @param bool $isNetPrice
     * @param FeVariant $feVariant
     */
    public function __construct(
        string $productType,
        int $productId,
        string $sku,
        string $title,
        float $price,
        TaxClass $taxClass,
        int $quantity,
        bool $isNetPrice = false,
        FeVariant $feVariant = null
    ) {
        $this->productType = $productType;
        $this->productId = $productId;
        $this->sku = $sku;
        $this->title = $title;
        $this->price = $price;
        $this->taxClass = $taxClass;
        $this->quantity = $quantity;
        $this->isNetPrice = $isNetPrice;

        if ($feVariant) {
            $this->feVariant = $feVariant;
        }

        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
    }

    /**
     * @param Cart $cart
     */
    public function setCart(Cart $cart)
    {
        $this->cart = $cart;
    }

    /**
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * @param string $sku
     */
    public function setSku(string $sku)
    {
        $this->sku = $sku;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @return bool
     */
    public function getIsNetPrice(): bool
    {
        return $this->isNetPrice;
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
     * @param BeVariant $newVariant
     */
    public function addBeVariant(BeVariant $newVariant)
    {
        $newVariantsId = $newVariant->getId();

        if (!empty($this->beVariants) && array_key_exists($newVariantsId, $this->beVariants)) {
            $variant = $this->beVariants[$newVariantsId];
            if ($variant->getBeVariants()) {
                $variant->addBeVariants($newVariant->getBeVariants());
            } else {
                $newQuantity = $variant->getQuantity() + $newVariant->getQuantity();
                $variant->setQuantity($newQuantity);
            }
        } else {
            $newVariant->setProduct($this);
            $this->beVariants[$newVariantsId] = $newVariant;
        }

        $this->reCalc();
    }

    /**
     * @param array $variantQuantity
     */
    public function changeVariantsQuantity(array $variantQuantity)
    {
        foreach ($variantQuantity as $variantId => $quantity) {
            /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
            $variant = $this->beVariants[$variantId];

            if (ctype_digit($quantity)) {
                $quantity = intval($quantity);
                $variant->changeQuantity($quantity);
            } elseif (is_array($quantity)) {
                $variant->changeVariantsQuantity($quantity);
            }

            $this->reCalc();
        }
    }

    /**
     * @return FeVariant|null
     */
    public function getFeVariant(): ?FeVariant
    {
        return $this->feVariant;
    }

    /**
     * @return array
     */
    public function getBeVariants(): array
    {
        return $this->beVariants;
    }

    /**
     * @param string $variantId
     * @return BeVariant|null
     */
    public function getBeVariantById(string $variantId): ?BeVariant
    {
        return $this->beVariants[$variantId];
    }

    /**
     * @param string $variantId
     * @return BeVariant
     */
    public function getBeVariant(string $variantId): ?BeVariant
    {
        return $this->getBeVariantById($variantId);
    }

    /**
     * @param array $variantsArray
     * @return int
     */
    public function removeBeVariants(array $variantsArray): int
    {
        foreach ($variantsArray as $variantId => $value) {
            /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
            $variant = $this->beVariants[$variantId];
            if ($variant) {
                if (is_array($value)) {
                    $variant->removeBeVariants($value);

                    if (!$variant->getBeVariants()) {
                        unset($this->beVariants[$variantId]);
                    }

                    $this->reCalc();
                } else {
                    unset($this->beVariants[$variantId]);

                    $this->reCalc();
                }
            } else {
                return -1;
            }
        }

        return 1;
    }

    /**
     * @param string $variantId
     */
    public function removeVariantById(string $variantId)
    {
        unset($this->beVariants[$variantId]);

        $this->reCalc();
    }

    /**
     * @param string $variantId
     */
    public function removeVariant(string $variantId)
    {
        $this->removeVariantById($variantId);
    }

    /**
     * @param string $variantId
     * @param int $newQuantity
     * @internal param $id
     */
    public function changeVariantById(string $variantId, int $newQuantity)
    {
        $this->beVariants[$variantId]->changeQuantity($newQuantity);

        $this->reCalc();
    }

    /**
     * @return string
     */
    public function getProductType(): string
    {
        return $this->productType;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $id = $this->getTableProductId();
    }

    /**
     * @return string
     */
    protected function getTableProductId(): string
    {
        $tableProductId = $this->getProductType() . '_' . $this->getProductId();
        if ($this->getFeVariant()) {
            $tableProductId .= '_' . $this->getFeVariant()->getId();
        }
        return $tableProductId;
    }

    /**
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * @return float
     */
    public function getTranslatedPrice(): float
    {
        if ($this->cart) {
            return $this->cart->translatePrice($this->getPrice());
        }

        return $this->getPrice();
    }

    /**
     * Returns Special Price
     *
     * @return float|null
     */
    public function getSpecialPrice(): ?float
    {
        return $this->specialPrice;
    }

    /**
     * @return float|null
     */
    public function getTranslatedSpecialPrice(): ?float
    {
        if ($this->cart) {
            return $this->cart->translatePrice($this->getSpecialPrice());
        }

        return $this->getSpecialPrice();
    }

    /**
     * Sets Special Price
     *
     * @param float $specialPrice
     */
    public function setSpecialPrice(float $specialPrice)
    {
        $this->specialPrice = $specialPrice;
    }

    /**
     * Returns Quantity Discounts
     *
     * @return array
     */
    public function getQuantityDiscounts(): array
    {
        return $this->quantityDiscounts;
    }

    /**
     * Returns Quantity Discount Price
     *
     * @param int $quantity
     *
     * @return float
     */
    public function getQuantityDiscountPrice(int $quantity = null): float
    {
        $price = $this->getTranslatedPrice();

        $quantity = $quantity ? $quantity : $this->getQuantity();

        if ($this->getQuantityDiscounts()) {
            foreach ($this->getQuantityDiscounts() as $quantityDiscount) {
                if (($quantityDiscount['quantity'] <= $quantity) && ($quantityDiscount['price'] < $price)) {
                    $price = $quantityDiscount['price'];
                }
            }
        }

        return $price;
    }

    /**
     * Set Quantity Discounts
     *
     * @param array $quantityDiscounts
     */
    public function setQuantityDiscounts(array $quantityDiscounts)
    {
        $this->quantityDiscounts = $quantityDiscounts;
    }

    /**
     * Returns Best Price (min of Price and Special Price)
     *
     * @param int $quantity
     *
     * @return float
     */
    public function getBestPrice(int $quantity = null)
    {
        $bestPrice = $this->getQuantityDiscountPrice($quantity);

        if ($this->getTranslatedSpecialPrice() && ($this->getTranslatedSpecialPrice() < $bestPrice)) {
            $bestPrice = $this->getTranslatedSpecialPrice();
        }

        return $bestPrice;
    }

    /**
     * Returns Best Price Discount
     *
     * @return float
     */
    public function getDiscount(): float
    {
        $discount = $this->getTranslatedPrice() - $this->getBestPrice();

        return $discount;
    }

    /**
     * Returns Special Price
     *
     * @return float
     */
    public function getSpecialPriceDiscount(): float
    {
        $discount = 0.0;
        if (($this->getTranslatedPrice() != 0.0) && ($this->getTranslatedSpecialPrice())) {
            $discount = (($this->getTranslatedPrice() - $this->getTranslatedSpecialPrice()) / $this->getTranslatedPrice()) * 100;
        }
        return $discount;
    }

    /**
     * @return float
     */
    public function getPriceTax(): float
    {
        return ($this->getBestPrice() / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
    }

    /**
     * @return TaxClass
     */
    public function getTaxClass(): TaxClass
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
     * @param int $newQuantity
     */
    public function changeQuantity(int $newQuantity)
    {
        if ($this->quantity != $newQuantity) {
            if ($newQuantity === 0) {
                $this->cart->removeProduct($this);
            } else {
                $this->quantity = $newQuantity;
                $this->reCalc();
            }
        }
    }

    /**
     * @param array $newQuantities
     */
    public function changeQuantities(array $newQuantities)
    {
        foreach ($newQuantities as $newQuantityKey => $newQuantityValue) {
            if ($newQuantityValue === 0) {
                $this->removeVariantById($newQuantityKey);
            } else {
                $this->getBeVariant($newQuantityKey)->setQuantity($newQuantityValue);
                $this->reCalc();
            }
        }
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * Quantity Leaving Range
     *
     * returns 0 if quantity is in rage
     * returns -1 if minimum was not reached
     * returns 1 if maximum was exceeded
     *
     * @return int
     */
    protected function isQuantityLeavingRange(): int
    {
        $outOfRange = 0;

        if ($this->getQuantityBelowRange()) {
            $outOfRange = -1;
        }
        if ($this->getQuantityAboveRange()) {
            $outOfRange = 1;
        }

        return $outOfRange;
    }

    /**
     * Quantity In Range
     *
     * @return bool
     */
    protected function isQuantityInRange(): bool
    {
        if ($this->isQuantityLeavingRange() != 0) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     */
    public function getQuantityIsInRange(): bool
    {
        return $this->isQuantityInRange();
    }

    /**
     * @return int
     */
    public function getQuantityIsLeavingRange(): int
    {
        return $this->isQuantityLeavingRange();
    }

    /**
     * Returns true if Quantity in cart is below Min Number In Cart
     * @return bool
     */
    public function getQuantityBelowRange(): bool
    {
        if ($this->quantity < $this->minNumberInCart) {
            return true;
        }

        return false;
    }

    /**
     * Returns true if Quantity in cart is above Max Number In Cart
     *
     * @return bool
     */
    public function getQuantityAboveRange(): bool
    {
        if (($this->maxNumberInCart > 0) && ($this->quantity > $this->maxNumberInCart)) {
            return true;
        }

        return false;
    }

    /**
     * @return float
     */
    public function getGross(): float
    {
        $gross = 0.0;
        if ($this->isQuantityInRange()) {
            $this->calcGross();
            $gross = $this->gross;
        }
        return $gross;
    }

    /**
     * @return float
     */
    public function getNet(): float
    {
        $net = 0.0;
        if ($this->isQuantityInRange()) {
            $this->calcNet();
            $net = $this->net;
        }
        return $net;
    }

    /**
     * @return float
     */
    public function getTax(): float
    {
        $tax = 0.0;
        if ($this->isQuantityInRange()) {
            $this->calcTax();
            $tax = $this->tax;
        }
        return $tax;
    }

    /**
     * @return mixed
     */
    public function getError()
    {
        return $this->error;
    }

    /**
     * @return bool
     */
    public function getIsVirtualProduct(): bool
    {
        return $this->isVirtualProduct;
    }

    /**
     * @param bool $isVirtualProduct
     */
    public function setIsVirtualProduct(bool $isVirtualProduct)
    {
        $this->isVirtualProduct = $isVirtualProduct;
    }

    /**
     * @return float|null
     */
    public function getServiceAttribute1(): ?float
    {
        return $this->serviceAttribute1;
    }

    /**
     * @param float $serviceAttribute1
     */
    public function setServiceAttribute1(float $serviceAttribute1)
    {
        $this->serviceAttribute1 = $serviceAttribute1;
    }

    /**
     * @return float|null
     */
    public function getServiceAttribute2(): ?float
    {
        return $this->serviceAttribute2;
    }

    /**
     * @param float $serviceAttribute2
     */
    public function setServiceAttribute2(float $serviceAttribute2)
    {
        $this->serviceAttribute2 = $serviceAttribute2;
    }

    /**
     * @return float|null
     */
    public function getServiceAttribute3(): ?float
    {
        return $this->serviceAttribute3;
    }

    /**
     * @param float $serviceAttribute3
     */
    public function setServiceAttribute3(float $serviceAttribute3)
    {
        $this->serviceAttribute3 = $serviceAttribute3;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $productArr = [
            'productType' => $this->productType,
            'productId' => $this->productId,
            'id' => $this->getId(),
            'sku' => $this->sku,
            'title' => $this->title,
            'price' => $this->price,
            'specialPrice' => $this->specialPrice,
            'quantityDiscounts' => $this->quantityDiscounts,
            'taxClass' => $this->taxClass,
            'quantity' => $this->quantity,
            'price_total' => $this->gross,
            'price_total_gross' => $this->gross,
            'price_total_net' => $this->net,
            'tax' => $this->tax,
            'additional' => $this->additional
        ];

        if ($this->beVariants) {
            $variantArr = [];

            foreach ($this->beVariants as $variant) {
                /** @var $variant \Extcode\Cart\Domain\Model\Cart\BeVariant */
                array_push($variantArr, [$variant->getId() => $variant->toArray()]);
            }

            array_push($productArr, ['variants' => $variantArr]);
        }

        return $productArr;
    }

    /**
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    protected function calcGross()
    {
        if ($this->isNetPrice == false) {
            if ($this->beVariants) {
                $sum = 0.0;
                foreach ($this->beVariants as $variant) {
                    /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
                    $sum += $variant->getGross();
                }
                $this->gross = $sum;
            } else {
                $this->gross = $this->getBestPrice() * $this->quantity;
            }
        } else {
            $this->calcNet();
            $this->calcTax();
            $this->gross = $this->net + $this->tax;
        }
    }

    protected function calcTax()
    {
        if ($this->isNetPrice == false) {
            $this->tax = ($this->gross / (1 + $this->getTaxClass()->getCalc())) * ($this->getTaxClass()->getCalc());
        } else {
            $this->tax = ($this->net * $this->getTaxClass()->getCalc());
        }
    }

    protected function calcNet()
    {
        if ($this->isNetPrice == true) {
            if ($this->beVariants) {
                $sum = 0.0;
                foreach ($this->beVariants as $variant) {
                    /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
                    $sum += $variant->getNet();
                }
                $this->net = $sum;
            } else {
                $this->net = $this->getBestPrice() * $this->quantity;
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
            foreach ($this->beVariants as $variant) {
                /** @var \Extcode\Cart\Domain\Model\Cart\BeVariant $variant */
                $quantity += $variant->getQuantity();
            }

            if ($this->quantity != $quantity) {
                $this->quantity = $quantity;
            }
        }

        $this->calcGross();
        $this->calcTax();
        $this->calcNet();
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
    public function getMinNumberInCart(): int
    {
        return $this->minNumberInCart;
    }

    /**
     * @param int $minNumberInCart
     */
    public function setMinNumberInCart(int $minNumberInCart)
    {
        if ($minNumberInCart < 0 ||
            ($this->maxNumberInCart > 0 && $minNumberInCart > $this->maxNumberInCart)
        ) {
            throw new \InvalidArgumentException;
        }

        $this->minNumberInCart = $minNumberInCart;
    }

    /**
     * @return int
     */
    public function getMaxNumberInCart(): int
    {
        return $this->maxNumberInCart;
    }

    /**
     * @param int $maxNumberInCart
     */
    public function setMaxNumberInCart(int $maxNumberInCart)
    {
        if ($maxNumberInCart < 0 || $maxNumberInCart < $this->minNumberInCart) {
            throw new \InvalidArgumentException;
        }

        $this->maxNumberInCart = $maxNumberInCart;
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

    /**
     * @return bool
     */
    public function isHandleStock(): bool
    {
        return $this->handleStock;
    }

    /**
     * @param bool $handleStock
     */
    public function setHandleStock(bool $handleStock)
    {
        $this->handleStock = $handleStock;
    }

    /**
     * @return bool
     */
    public function isHandleStockInVariants(): bool
    {
        return $this->handleStockInVariants;
    }

    /**
     * @param bool $handleStockInVariants
     */
    public function setHandleStockInVariants(bool $handleStockInVariants)
    {
        $this->handleStockInVariants = $handleStockInVariants;
    }
}
