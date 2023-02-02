<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Cart\UpdateCurrencyEventInterface;

class UpdateCurrency
{
    public function __invoke(UpdateCurrencyEventInterface $event): void
    {
        $cart = $event->getCart();
        $request = $event->getRequest();
        $settings = $event->getSettings();

        $currencyCode = '';

        if ($request->hasArgument('currencyCode')) {
            $currencyCode = $request->getArgument('currencyCode');
        }

        $currencyConfigId = $this->getCurrencyConfigId($currencyCode, $settings);

        if ($currencyConfigId) {
            $cart->setCurrencyCode($currencyCode);
            $cart->setCurrencySign(
                $settings[$currencyConfigId]['sign']
            );

            $cart->setCurrencyTranslation(
                (float)($settings[$currencyConfigId]['translation'])
            );
        }

        $cart->reCalc();
    }

    protected function getCurrencyConfigId(string $currencyCode, array $settings): int
    {
        if (strlen($currencyCode) === 3) {
            foreach ($settings as $currencyConfigId => $currency) {
                if (is_array($currency) && $currency['code'] === $currencyCode) {
                    return (int)$currencyConfigId;
                }
            }
        }

        return 0;
    }
}
