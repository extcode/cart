<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Loader\SiteSets;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\PaymentMethodsLoaderInterface;
use Extcode\Cart\Domain\Model\Cart\Cart;

final readonly class PaymentMethodsLoader extends AbstractConfigurationLoader implements PaymentMethodsLoaderInterface
{
    public function getPaymentMethods(Cart $cart): array
    {
        $services = [];

        $configurations = $this->getConfigurationsForType('cart-payments', $cart->getBillingCountry());

        return $this->getServices($configurations, $services, $cart);
    }
}
