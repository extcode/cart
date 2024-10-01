<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Cart\UpdateCurrencyEvent;

class UpdateCurrency
{
    public function __invoke(UpdateCurrencyEvent $event): void
    {
        $cart = $event->getCart();
        $request = $event->getRequest();
        $settings = $event->getSettings();

        $currencyCode = '';

        if ($request->hasArgument('currencyCode')) {
            $currencyCode = $request->getArgument('currencyCode');
        }

        $currencyConfigId = $this->getCurrencyConfigId($currencyCode, $settings['options']);

        if ($currencyConfigId) {
            $cart->setCurrencyCode($currencyCode);
            $cart->setCurrencySign(
                $settings['options'][$currencyConfigId]['sign']
            );

            $cart->setCurrencyTranslation(
                (float)($settings['options'][$currencyConfigId]['translation'])
            );
        }

        $cart->reCalc();
    }

    protected function getCurrencyConfigId(string $currencyCode, array $currencyOptions): int
    {
        if (strlen($currencyCode) === 3) {
            foreach ($currencyOptions as $currencyOptionId => $currencyOption) {
                if (is_array($currencyOption) && $currencyOption['code'] === $currencyCode) {
                    return (int)$currencyOptionId;
                }
            }
        }

        return 0;
    }
}
