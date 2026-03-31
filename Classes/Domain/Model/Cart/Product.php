<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

final class Product implements AdditionalDataInterface, ProductInterface
{
    use AdditionalDataTrait;

    protected ?Cart $cart = null;

    protected ?float $specialPrice = null;

    protected array $quantityDiscounts = [];

    protected float $gross;

    protected float $net;

    protected float $tax;

    protected string $error;

    protected bool $isVirtualProduct = false;

    protected ?float $serviceAttribute1 = null;

    protected ?float $serviceAttribute2 = null;

    protected ?float $serviceAttribute3 = null;

    /**
     * @var array<string, BeVariantInterface>
     */
    protected array $beVariants = [];

    protected ?FeVariantInterface $feVariant = null;

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

    protected DetailPageLink $detailPageLink;

    public function __construct(
        protected string $productType,
        protected int $productId,
        protected string $sku,
        protected string $title,
        protected float $price,
        protected TaxClass $taxClass,
        protected int $quantity,
        protected bool $isNetPrice = false,
        ?FeVariantInterface $feVariant = null
    ) {
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

    /**
     * @param BeVariantInterface[] $newVariants
     */
    public function addBeVariants(array $newVariants): void
    {
        foreach ($newVariants as $newVariant) {
            $this->addBeVariant($newVariant);
        }
    }

    public function addBeVariant(BeVariantInterface $newVariant): void
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
            $newVariant->setParent($this);
            $this->beVariants[$newVariantsId] = $newVariant;
        }

        $this->reCalc();
    }

    public function changeVariantsQuantity(array $variantQuantity): void
    {
        foreach ($variantQuantity as $variantId => $quantity) {
            $variant = $this->beVariants[$variantId];

            if (ctype_digit((string)$quantity)) {
                $quantity = (int)$quantity;
                $variant->changeQuantity($quantity);
            } elseif (is_array($quantity)) {
                $variant->changeVariantsQuantity($quantity);
            }

            $this->reCalc();
        }
    }

    public function getFeVariant(): ?FeVariantInterface
    {
        return $this->feVariant;
    }

    /**
     * @return BeVariantInterface[]
     */
    public function getBeVariants(): array
    {
        return $this->beVariants;
    }

    public function getBeVariantById(string $variantId): ?BeVariantInterface
    {
        return $this->beVariants[$variantId] ?? null;
    }

    /**
     * @param array<string, mixed> $variantsArray
     */
    public function removeBeVariants(array $variantsArray): int
    {
        foreach ($variantsArray as $variantId => $value) {
            if (isset($this->beVariants[$variantId])) {
                $variant = $this->beVariants[$variantId];
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

    public function getQuantityDiscountPrice(?int $quantity = null): float
    {
        $price = $this->getTranslatedPrice();

        $quantity = $quantity ?: $this->getQuantity();

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
    public function getBestPrice(?int $quantity = null): ?float
    {
        $bestPrice = $this->getQuantityDiscountPrice($quantity);

        $translatedSpecialPrice = $this->getTranslatedSpecialPrice();
        if ($translatedSpecialPrice !== null && $translatedSpecialPrice < $bestPrice) {
            $bestPrice = $translatedSpecialPrice;
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

    public function isQuantityBelowRange(): bool
    {
        return $this->quantity < $this->minNumberInCart;
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
            'additionals' => $this->getAdditionals(),
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
                    $sum += $variant->getGross();
                }
                if ($this->feVariant instanceof FeVariantWithPriceInterface) {
                    $sum += $this->feVariant->getPrice();
                }
                $this->gross = $sum;
            } else {
                $sum = $this->getBestPrice();
                if ($this->feVariant instanceof FeVariantWithPriceInterface) {
                    $sum += $this->feVariant->getPrice();
                }
                $this->gross = $sum * $this->quantity;
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
                    $sum += $variant->getNet();
                }
                if ($this->feVariant instanceof FeVariantWithPriceInterface) {
                    $sum += $this->feVariant->getPrice();
                }
                $this->net = $sum;
            } else {
                $sum = $this->getBestPrice();
                if ($this->feVariant instanceof FeVariantWithPriceInterface) {
                    $sum += $this->feVariant->getPrice();
                }
                $this->net = $sum * $this->quantity;
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

    public function getMinNumberInCart(): int
    {
        return $this->minNumberInCart;
    }

    public function setMinNumberInCart(int $minNumberInCart): void
    {
        if ($minNumberInCart < 0
            || ($this->maxNumberInCart > 0 && $minNumberInCart > $this->maxNumberInCart)
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

    public function getDetailPageLink(): DetailPageLink
    {
        return $this->detailPageLink;
    }

    public function setDetailPageLink(DetailPageLink $detailPageLink): void
    {
        $this->detailPageLink = $detailPageLink;
    }
}
