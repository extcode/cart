<?php

namespace Extcode\Cart\Controller\Cart;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Cart Currency Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
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

            $assignArguments = [
                'shippings' => $this->shippings,
                'payments' => $this->payments,
                'specials' => $this->specials
            ];
            $this->view->assignMultiple($assignArguments);
        }
    }
}
