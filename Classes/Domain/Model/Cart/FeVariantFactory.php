<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class FeVariantFactory implements FeVariantFactoryInterface
{
    public function create(
        array $variantData = []
    ): FeVariantInterface {
        return new FeVariant(
            $variantData,
        );
    }
}
