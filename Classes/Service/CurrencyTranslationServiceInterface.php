<?php

declare(strict_types=1);

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * @internal This class is marked internal and is not considered part of the public API. The interface will change in the next major version (v12.0.0).
 */
interface CurrencyTranslationServiceInterface
{
    public function translatePrice(float $factor, ?float $price = null): ?float;
}
