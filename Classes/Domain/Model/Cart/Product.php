<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Product
{
    protected string $productType;

    protected int $productId;

    protected ?Cart $cart = null;

    protected ?string $title = null;

    protected ?string $sku = null;

    protected ?float $price = null;

    protected ?float $specialPrice = null;

    protected array $quantityDiscounts = [];

    protected ?int $quantity = null;

    protected float $gross;

    protected float $net;

    protected TaxClass $taxClass;

    protected float $tax;

    protected string $error;

    protected bool $isVirtualProduct = false;

    protected ?float $serviceAttribute1 = null;

    protected ?float $serviceAttribute2 = null;

    protected ?float $serviceAttribute3 = null;

    protected bool $isNetPrice = false;

    protected array $beVariants = [];

    protected ?FeVariant $feVariant = null;

    protected array $additional = [];

    protected int $minNumberInCart = 0;

    protected int $maxNumberInCart = 0;

    /**
     * Number of products in stock.
     */
    protected int $stock = 0;

    /**
     * Is the stock management active for this product?
     */
    protected bool $handleStock = false;

    /**
     * Is the stock management for this product covered in the backend variants?
     */
    protected bool $handleStockInVariants = false;

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

    public function setCart(Cart $cart): void
    {
        $this->cart = $cart;
    }

    public function getSku(): string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function isNetPrice(): bool
    {
        return $this->isNetPrice;
    }

    public function addBeVariants(array $newVariants): void
    {
        foreach ($newVariants as $newVariant) {
            $this->addBeVariant($newVariant);
        }
    }

    public function addBeVariant(BeVariant $newVariant): void
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

    public function changeVariantsQuantity(array $variantQuantity): void
    {
        foreach ($variantQuantity as $variantId => $quantity) {
            /** @var BeVariant $variant */
            $variant = $this->beVariants[$variantId];

            if (ctype_digit($quantity)) {
                $quantity = (int)$quantity;
                $variant->changeQuantity($quantity);
            } elseif (is_array($quantity)) {
                $variant->changeVariantsQuantity($quantity);
            }

            $this->reCalc();
        }
    }

    public function getFeVariant(): ?FeVariant
    {
        return $this->feVariant;
    }

    public function getBeVariants(): array
    {
        return $this->beVariants;
    }

    public function getBeVariantById(string $variantId): ?BeVariant
    {
        if (isset($this->beVariants[$variantId])) {
            return $this->beVariants[$variantId];
        }

        return null;
    }

    public function removeBeVariants(array $variantsArray): int
    {
        foreach ($variantsArray as $variantId => $value) {
            /** @var BeVariant $variant */
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

    public function removeVariantById(string $variantId): void
    {
        unset($this->beVariants[$variantId]);

        $this->reCalc();
    }

    public function removeVariant(string $variantId): void
    {
        $this->removeVariantById($variantId);
    }

    public function changeVariantById(string $variantId, int $newQuantity): void
    {
        $this->beVariants[$variantId]->changeQuantity($newQuantity);

        $this->reCalc();
    }

    public function getProductType(): string
    {
        return $this->productType;
    }

    public function getProductId(): int
    {
        return $this->productId;
    }

    public function getId(): string
    {
        return $this->getTableProductId();
    }

    protected function getTableProductId(): string
    {
        $tableProductId = $this->getProductType() . '_' . $this->getProductId();
        if ($this->getFeVariant()) {
            $tableProductId .= '_' . $this->getFeVariant()->getId();
        }
        return $tableProductId;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function getTranslatedPrice(): float
    {
        if ($this->cart) {
            return $this->cart->translatePrice($this->getPrice());
        }

        return $this->getPrice();
    }

    public function getSpecialPrice(): ?float
    {
        return $this->specialPrice;
    }

    public function getTranslatedSpecialPrice(): ?float
    {
        if ($this->cart) {
            return $this->cart->translatePrice($this->getSpecialPrice());
        }

        return $this->getSpecialPrice();
    }

    public function setSpecialPrice(float $specialPrice): void
    {
        $this->specialPrice = $specialPrice;
    }

    public function getQuantityDiscounts(): array
    {
        return $this->quantityDiscounts;
    }

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

    public function setQuantityDiscounts(array $quantityDiscounts): void
    {
        $this->quantityDiscounts = $quantityDiscounts;
    }

    /**
     * Returns Best Price (min of Price and Special Price)
     */
    public function getBestPrice(int $quantity = null): ?float
    {
        $bestPrice = $this->getQuantityDiscountPrice($quantity);

        if ($this->getTranslatedSpecialPrice() && ($this->getTranslatedSpecialPrice() < $bestPrice)) {
            $bestPrice = $this->getTranslatedSpecialPrice();
        }

        return $bestPrice;
    }

    /**
     * Returns Best Price Discount
     */
    public function getDiscount(): float
    {
        $discount = $this->getTranslatedPrice() - $this->getBestPrice();

        return $discount;
    }

    /**
     * Returns Special Price
     */
    public function getSpecialPriceDiscount(): float
    {
        $discount = 0.0;
        if (($this->getTranslatedPrice() != 0.0) && ($this->getTranslatedSpecialPrice())) {
            $discount = (($this->getTranslatedPrice() - $this->getTranslatedSpecialPrice()) / $this->getTranslatedPrice()) * 100;
        }
        return $discount;
    }

    public function getPriceTax(): float
    {
        return ($this->getBestPrice() / (1 + $this->taxClass->getCalc())) * ($this->taxClass->getCalc());
    }

    public function getTaxClass(): TaxClass
    {
        return $this->taxClass;
    }

    public function setTaxClass(TaxClass $taxClass): void
    {
        $this->taxClass = $taxClass;
    }

    public function changeQuantity(int $newQuantity): void
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

    public function changeQuantities(array $newQuantities): void
    {
        foreach ($newQuantities as $newQuantityKey => $newQuantityValue) {
            $newQuantityKey = (string)$newQuantityKey;
            $newQuantityValue = (int)$newQuantityValue;
            if ($newQuantityValue === 0) {
                $this->removeVariantById((string)$newQuantityKey);
            } else {
                $this->getBeVariantById((string)$newQuantityKey)->setQuantity($newQuantityValue);
                $this->reCalc();
            }
        }
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function isQuantityInRange(): bool
    {
        return !$this->isQuantityBelowRange() && !$this->isQuantityAboveRange();
    }

    /**
     * returns 0 if quantity is in rage
     * returns -1 if minimum was not reached
     * returns 1 if maximum was exceeded
     *
     * @deprecated
     */
    public function getQuantityIsLeavingRange(): int
    {
        if ($this->getQuantityBelowRange()) {
            return -1;
        }
        if ($this->getQuantityAboveRange()) {
            return 1;
        }

        return 0;
    }

    /**
     * @deprecated
     */
    public function getQuantityBelowRange(): bool
    {
        return $this->isQuantityBelowRange();
    }

    public function isQuantityBelowRange(): bool
    {
        return $this->quantity < $this->minNumberInCart;
    }

    /**
     * @deprecated
     */
    public function getQuantityAboveRange(): bool
    {
        return $this->isQuantityAboveRange();
    }

    public function isQuantityAboveRange(): bool
    {
        return ($this->maxNumberInCart > 0) && ($this->quantity > $this->maxNumberInCart);
    }

    public function getGross(): float
    {
        $gross = 0.0;
        if ($this->isQuantityInRange()) {
            $this->calcGross();
            $gross = $this->gross;
        }
        return $gross;
    }

    public function getNet(): float
    {
        $net = 0.0;
        if ($this->isQuantityInRange()) {
            $this->calcNet();
            $net = $this->net;
        }
        return $net;
    }

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

    public function isVirtualProduct(): bool
    {
        return $this->isVirtualProduct;
    }

    public function setIsVirtualProduct(bool $isVirtualProduct): void
    {
        $this->isVirtualProduct = $isVirtualProduct;
    }

    public function getServiceAttribute1(): ?float
    {
        return $this->serviceAttribute1;
    }

    public function setServiceAttribute1(float $serviceAttribute1): void
    {
        $this->serviceAttribute1 = $serviceAttribute1;
    }

    public function getServiceAttribute2(): ?float
    {
        return $this->serviceAttribute2;
    }

    public function setServiceAttribute2(float $serviceAttribute2): void
    {
        $this->serviceAttribute2 = $serviceAttribute2;
    }

    public function getServiceAttribute3(): ?float
    {
        return $this->serviceAttribute3;
    }

    public function setServiceAttribute3(float $serviceAttribute3): void
    {
        $this->serviceAttribute3 = $serviceAttribute3;
    }

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
            'additional' => $this->additional,
        ];

        if ($this->beVariants) {
            $variantArr = [];

            foreach ($this->beVariants as $variant) {
                $variantArr[] = [$variant->getId() => $variant->toArray()];
            }

            $productArr[] = ['variants' => $variantArr];
        }

        return $productArr;
    }

    public function toJson(): string
    {
        return json_encode($this->toArray());
    }

    protected function calcGross(): void
    {
        if ($this->isNetPrice == false) {
            if ($this->beVariants) {
                $sum = 0.0;
                foreach ($this->beVariants as $variant) {
                    /** @var BeVariant $variant */
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

    protected function calcTax(): void
    {
        if ($this->isNetPrice == false) {
            $this->tax = ($this->gross / (1 + $this->getTaxClass()->getCalc())) * ($this->getTaxClass()->getCalc());
        } else {
            $this->tax = ($this->net * $this->getTaxClass()->getCalc());
        }
    }

    protected function calcNet(): void
    {
        if ($this->isNetPrice == true) {
            if ($this->beVariants) {
                $sum = 0.0;
                foreach ($this->beVariants as $variant) {
                    /** @var BeVariant $variant */
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

    protected function reCalc(): void
    {
        if ($this->beVariants) {
            $quantity = 0;
            foreach ($this->beVariants as $variant) {
                /** @var BeVariant $variant */
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
     * @param mixed $value
     */
    public function setAdditional(string $key, $value): void
    {
        $this->additional[$key] = $value;
    }

    public function getMinNumberInCart(): int
    {
        return $this->minNumberInCart;
    }

    public function setMinNumberInCart(int $minNumberInCart): void
    {
        if ($minNumberInCart < 0 ||
            ($this->maxNumberInCart > 0 && $minNumberInCart > $this->maxNumberInCart)
        ) {
            throw new \InvalidArgumentException();
        }

        $this->minNumberInCart = $minNumberInCart;
    }

    public function getMaxNumberInCart(): int
    {
        return $this->maxNumberInCart;
    }

    public function setMaxNumberInCart(int $maxNumberInCart): void
    {
        if ($maxNumberInCart < 0 || $maxNumberInCart < $this->minNumberInCart) {
            throw new \InvalidArgumentException();
        }

        $this->maxNumberInCart = $maxNumberInCart;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): void
    {
        $this->stock = $stock;
    }

    public function isHandleStock(): bool
    {
        return $this->handleStock;
    }

    public function setHandleStock(bool $handleStock): void
    {
        $this->handleStock = $handleStock;
    }

    public function isHandleStockInVariants(): bool
    {
        return $this->handleStockInVariants;
    }

    public function setHandleStockInVariants(bool $handleStockInVariants): void
    {
        $this->handleStockInVariants = $handleStockInVariants;
    }
}
