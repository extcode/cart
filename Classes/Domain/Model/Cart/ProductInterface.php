<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

interface ProductInterface extends MinimalInterface
{
    public function setCart(Cart $cart): void;

    public function isNetPrice(): bool;

    /**
     * @param BeVariantInterface[] $newVariants
     */
    public function addBeVariants(array $newVariants): void;

    public function addBeVariant(BeVariantInterface $newVariant): void;

    public function changeVariantsQuantity(array $variantQuantity): void;

    public function getFeVariant(): ?FeVariantInterface;

    /**
     * @return BeVariantInterface[]
     */
    public function getBeVariants(): array;

    public function getBeVariantById(string $variantId): ?BeVariantInterface;

    /**
     * @param BeVariantInterface[] $variantsArray
     */
    public function removeBeVariants(array $variantsArray): int;

    public function removeVariantById(string $variantId): void;

    public function removeVariant(string $variantId): void;

    public function changeVariantById(string $variantId, int $newQuantity): void;

    public function getProductType(): string;

    public function getProductId(): int;

    public function getPrice(): float;

    public function getTranslatedPrice(): float;

    public function getSpecialPrice(): ?float;

    public function getTranslatedSpecialPrice(): ?float;

    public function setSpecialPrice(float $specialPrice): void;

    public function getQuantityDiscounts(): array;

    public function getQuantityDiscountPrice(?int $quantity = null): float;

    public function setQuantityDiscounts(array $quantityDiscounts): void;

    /**
     * Returns Best Price (min of Price and Special Price)
     */
    public function getBestPrice(?int $quantity = null): ?float;

    /**
     * Returns Best Price Discount
     */
    public function getDiscount(): float;

    /**
     * Returns Special Price
     */
    public function getSpecialPriceDiscount(): float;

    public function getPriceTax(): float;

    public function getTaxClass(): TaxClass;

    public function setTaxClass(TaxClass $taxClass): void;

    public function changeQuantity(int $newQuantity): void;

    public function changeQuantities(array $newQuantities): void;

    public function getQuantity(): int;

    public function isQuantityInRange(): bool;

    public function isQuantityBelowRange(): bool;

    public function isQuantityAboveRange(): bool;

    public function getGross(): float;

    public function getNet(): float;

    public function getTax(): float;

    /**
     * @return mixed
     */
    public function getError();

    public function isVirtualProduct(): bool;

    public function setIsVirtualProduct(bool $isVirtualProduct): void;

    public function getServiceAttribute1(): ?float;

    public function setServiceAttribute1(float $serviceAttribute1): void;

    public function getServiceAttribute2(): ?float;

    public function setServiceAttribute2(float $serviceAttribute2): void;

    public function getServiceAttribute3(): ?float;

    public function setServiceAttribute3(float $serviceAttribute3): void;

    public function toArray(): array;

    public function toJson(): string;

    public function getMinNumberInCart(): int;

    public function setMinNumberInCart(int $minNumberInCart): void;

    public function getMaxNumberInCart(): int;

    public function setMaxNumberInCart(int $maxNumberInCart): void;

    public function getStock(): int;

    public function setStock(int $stock): void;

    public function isHandleStock(): bool;

    public function setHandleStock(bool $handleStock): void;

    public function isHandleStockInVariants(): bool;

    public function setHandleStockInVariants(bool $handleStockInVariants): void;
}
