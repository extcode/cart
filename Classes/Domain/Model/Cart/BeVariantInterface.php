<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

interface BeVariantInterface extends MinimalInterface
{
    public function toArray(): array;

    public function getParent(): BeVariantInterface|ProductInterface;

    public function setParent(BeVariantInterface|ProductInterface $parent): void;

    public function getProduct(): ProductInterface;

    public function isNetPrice(): bool;

    public function getCompleteTitle(): string;

    public function getCompleteTitleWithoutProduct(): string;

    public function getPrice(): float;

    public function setPrice(float $price): void;

    public function getSpecialPrice(): ?float;

    public function setSpecialPrice(float $specialPrice): void;

    public function getBestPrice(): float;

    public function getPriceCalculated(): float;

    public function getDiscount(): float;

    public function getSpecialPriceDiscount(): float;

    public function getBestPriceCalculated(): float;

    public function getPriceCalcMethod(): int;

    public function setPriceCalcMethod(int $priceCalcMethod): void;

    public function getCompleteSku(): string;

    public function getCompleteSkuWithoutProduct(): string;

    public function getQuantity(): int;

    public function getGross(): float;

    public function getNet(): float;

    public function getTax(): float;

    public function getTaxClass(): TaxClass;

    public function setQuantity(int $newQuantity): void;

    public function changeQuantity(int $newQuantity): void;

    public function changeVariantsQuantity(array $variantQuantityArray): void;

    public function addBeVariants(array $newVariants): void;

    public function addBeVariant(self $newBeVariant): void;

    public function getBeVariants(): array;

    public function getBeVariantById(int $beVariantId): ?self;

    /**
     * @return bool|int
     */
    public function removeBeVariants(array $beVariantsArray);

    public function getMin(): int;

    public function setMin(int $min): void;

    public function getMax(): int;

    public function setMax(int $max): void;

    public function getStock(): int;

    public function setStock(int $stock): void;
}
