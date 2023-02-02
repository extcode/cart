<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Utility\CurrencyUtility;
use Psr\Http\Message\ResponseInterface;

class CurrencyController extends ActionController
{
    public const AJAX_CART_TYPE_NUM = '2278001';
    public const AJAX_CURRENCY_TYPE_NUM = '2278003';

    protected CurrencyUtility $currencyUtility;

    public function injectCurrencyUtility(CurrencyUtility $currencyUtility): void
    {
        $this->currencyUtility = $currencyUtility;
    }

    public function updateAction(): ResponseInterface
    {
        $this->currencyUtility->updateCurrency($this->settings['cart'], $this->configurations, $this->request);

        $this->restoreSession();

        $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);

        $this->cartUtility->updateService($this->cart, $this->configurations);

        $pageType = $GLOBALS['TYPO3_REQUEST']->getAttribute('routing')->getPageType();
        if ($pageType === self::AJAX_CURRENCY_TYPE_NUM) {
            $this->view->assign('cart', $this->cart);
        } elseif ($pageType === self::AJAX_CART_TYPE_NUM) {
            $this->view->assign('cart', $this->cart);

            $this->parseServicesAndAssignToView();
        }

        return $this->htmlResponse();
    }
}
