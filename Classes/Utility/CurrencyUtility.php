<?php

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

class CurrencyUtility
{
    /**
     * @param array $cartSettings
     * @param array $pluginSettings
     * @param \TYPO3\CMS\Extbase\Mvc\Request $request
     */
    public function updateCurrency(array $cartSettings, array $pluginSettings, \TYPO3\CMS\Extbase\Mvc\Request $request)
    {
        $sessionHandler = GeneralUtility::makeInstance(
            \Extcode\Cart\Service\SessionHandler::class
        );
        $cart = $sessionHandler->restore($cartSettings['pid']);

        $currencyCode = '';

        if ($request->hasArgument('currencyCode')) {
            $currencyCode = $request->getArgument('currencyCode');
        }

        $currencyConfigId = $this->getCurrencyConfigId($currencyCode, $pluginSettings);

        if ($currencyConfigId) {
            $data = [
                'cart' => $cart,
                'currencyCode' => $currencyCode,
                'currencySign' => $pluginSettings['settings']['currencies'][$currencyConfigId]['sign'],
                'currencyTranslation' => floatval($pluginSettings['settings']['currencies'][$currencyConfigId]['translation'])
            ];

            $signalSlotDispatcher = GeneralUtility::makeInstance(
                \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
            );
            $signalSlotDispatcher->dispatch(
                __CLASS__,
                __FUNCTION__,
                $data
            );

            $cart->setCurrencyCode($currencyCode);
            $cart->setCurrencySign(
                $pluginSettings['settings']['currencies'][$currencyConfigId]['sign']
            );

            $cart->setCurrencyTranslation(
                floatval($pluginSettings['settings']['currencies'][$currencyConfigId]['translation'])
            );

            $cart->reCalc();

            $sessionHandler->write($cart, $cartSettings['pid']);
        }
    }

    /**
     * @param string $currencyCode
     * @param array $pluginSettings
     *
     * @return int
     */
    protected function getCurrencyConfigId($currencyCode, $pluginSettings)
    {
        if (strlen($currencyCode) == 3) {
            if (is_array($pluginSettings) &&
                is_array($pluginSettings['settings']) &&
                is_array($pluginSettings['settings']['currencies'])
            ) {
                foreach ($pluginSettings['settings']['currencies'] as $currencyConfigId => $currency) {
                    if (is_array($currency) && $currency['code'] == $currencyCode) {
                        return intval($currencyConfigId);
                    }
                }
            }
        }

        return 0;
    }
}
