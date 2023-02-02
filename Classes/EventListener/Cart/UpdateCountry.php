<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Cart\UpdateCountryEventInterface;

class UpdateCountry
{
    public function __invoke(UpdateCountryEventInterface $event): void
    {
        $cart = $event->getCart();
        $request = $event->getRequest();

        if ($request->hasArgument('billing_country')) {
            $billingCountry = $request->getArgument('billing_country');
            $cart->setBillingCountry($billingCountry);
        }

        if ($request->hasArgument('shipping_same_as_billing')) {
            $shippingSameAsBilling = $request->getArgument('shipping_same_as_billing') === 'true';
            $cart->setShippingSameAsBilling($shippingSameAsBilling);
        }

        if ($request->hasArgument('shipping_country')) {
            $shippingCountry = $request->getArgument('shipping_country');
            $cart->setShippingCountry($shippingCountry);
        }
    }
}
