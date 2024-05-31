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

class PaymentMethodsFromTypoScriptService extends AbstractConfigurationFromTypoScriptService implements PaymentMethodsServiceInterface
{
    public function getPaymentMethods(Cart $cart): array
    {
        $services = [];

        $configurations = $this->getConfigurationsForType('payments', $cart->getBillingCountry());

        return $this->getServices($configurations, $services, $cart);
    }
}
