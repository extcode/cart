<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

final readonly class BeVariantFactory implements BeVariantFactoryInterface
{
    public function create(
        string $id,
        BeVariantInterface|ProductInterface $parent,
        string $title,
        string $sku,
        int $priceCalcMethod,
        float $price,
        int $quantity = 0
    ): BeVariantInterface {
        return new BeVariant(
            $id,
            $parent,
            $title,
            $sku,
            $priceCalcMethod,
            $price,
            $quantity,
        );
    }
}
