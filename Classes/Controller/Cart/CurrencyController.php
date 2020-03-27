<?php

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class CurrencyController extends ActionController
{
    /**
     * Currency Utility
     *
     * @var \Extcode\Cart\Utility\CurrencyUtility
     */
    protected $currencyUtility;

    /**
     * @param \Extcode\Cart\Utility\CurrencyUtility $currencyUtility
     */
    public function injectCurrencyUtility(
        \Extcode\Cart\Utility\CurrencyUtility $currencyUtility
    ) {
        $this->currencyUtility = $currencyUtility;
    }

    /**
     *
     */
    public function updateAction()
    {
        $this->currencyUtility->updateCurrency($this->settings['cart'], $this->pluginSettings, $this->request);

        $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

        $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);

        $this->cartUtility->updateService($this->cart, $this->pluginSettings);

        if (isset($_GET['type']) && intval($_GET['type']) == 2278003) {
            $this->view->assign('cart', $this->cart);
        } elseif (isset($_GET['type']) && intval($_GET['type']) == 2278001) {
            $this->view->assign('cart', $this->cart);

            $this->parseData();

            $assignArguments = [
                'shippings' => $this->shippings,
                'payments' => $this->payments,
                'specials' => $this->specials
            ];
            $this->view->assignMultiple($assignArguments);
        }
    }
}
