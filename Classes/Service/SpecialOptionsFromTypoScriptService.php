<?php

declare(strict_types=1);

namespace Extcode\Cart\Service;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;

class SpecialOptionsFromTypoScriptService extends AbstractConfigurationFromTypoScriptService implements SpecialOptionsServiceInterface
{
    public function getSpecialOptions(Cart $cart): array
    {
        $services = [];

        $configurations = $this->getConfigurationsForType('specials', $cart->getCountry());

        return $this->getServices($configurations, $services, $cart);
    }
}
