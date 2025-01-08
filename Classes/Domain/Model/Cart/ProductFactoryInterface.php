<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

interface ProductFactoryInterface
{
    public function create(
        string $productType,
        int $productId,
        string $sku,
        string $title,
        float $price,
        TaxClass $taxClass,
        int $quantity,
        bool $isNetPrice = false,
        ?FeVariantInterface $feVariant = null
    ): ProductInterface;
}
