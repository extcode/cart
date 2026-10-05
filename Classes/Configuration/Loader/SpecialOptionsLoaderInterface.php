<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;

interface SpecialOptionsLoaderInterface
{
    public function getSpecialOptions(Cart $cart): array;
}
