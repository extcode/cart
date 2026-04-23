<?php

declare(strict_types=1);

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class CurrencyTranslationService implements CurrencyTranslationServiceInterface
{
    public function translatePrice(float $factor, ?float $price = null): ?float
    {
        if (is_null($price)) {
            return null;
        }

        return round($price / $factor * 100.0) / 100.0;
    }
}
