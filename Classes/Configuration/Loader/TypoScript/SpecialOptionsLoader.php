<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader\TypoScript;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\SpecialOptionsLoaderInterface;
use Extcode\Cart\Domain\Model\Cart\Cart;

final readonly class SpecialOptionsLoader extends AbstractConfigurationLoader implements SpecialOptionsLoaderInterface
{
    public function getSpecialOptions(Cart $cart): array
    {
        $services = [];

        $configurations = $this->getConfigurationsForType('specials', $cart->getCountry());

        return $this->getServices($configurations, $services, $cart);
    }
}
