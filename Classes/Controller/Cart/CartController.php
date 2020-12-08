<?php

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

class CartController extends ActionController
{
    /**
     * Stock Utility
     *
     * @var \Extcode\Cart\Utility\StockUtility
     */
    protected $stockUtility;

    /**
     * @param \Extcode\Cart\Utility\StockUtility $stockUtility
     */
    public function injectStockUtility(
        \Extcode\Cart\Utility\StockUtility $stockUtility
    ) {
        $this->stockUtility = $stockUtility;
    }

    /**
     * Action Show
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param \Extcode\Cart\Domain\Model\Order\BillingAddress $billingAddress
     * @param \Extcode\Cart\Domain\Model\Order\ShippingAddress $shippingAddress
     */
    public function showAction(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem = null,
        \Extcode\Cart\Domain\Model\Order\BillingAddress $billingAddress = null,
        \Extcode\Cart\Domain\Model\Order\ShippingAddress $shippingAddress = null
    ) {
        $this->restoreSession();

        if ($orderItem === null) {
            $orderItem = GeneralUtility::makeInstance(
                \Extcode\Cart\Domain\Model\Order\Item::class
            );
        }
        if ($billingAddress === null) {
            $billingAddress = GeneralUtility::makeInstance(
                \Extcode\Cart\Domain\Model\Order\BillingAddress::class
            );
        }
        if ($shippingAddress === null) {
            $shippingAddress = GeneralUtility::makeInstance(
                \Extcode\Cart\Domain\Model\Order\ShippingAddress::class
            );
        }

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['showCartActionAfterCartWasLoaded']) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['showCartActionAfterCartWasLoaded'] as $funcRef) {
                if ($funcRef) {
                    $params = [
                        'request' => $this->request,
                        'settings' => $this->settings,
                        'cart' => &$this->cart,
                        'orderItem' => &$orderItem,
                        'billingAddress' => &$billingAddress,
                        'shippingAddress' => &$shippingAddress,
                    ];

                    \TYPO3\CMS\Core\Utility\GeneralUtility::callUserFunction($funcRef, $params, $this);
                }
            }
        }

        $this->view->assign('cart', $this->cart);

        $this->parseData();

        $assignArguments = [
            'shippings' => $this->shippings,
            'payments' => $this->payments,
            'specials' => $this->specials
        ];
        $this->view->assignMultiple($assignArguments);

        $assignArguments = [
            'orderItem' => $orderItem,
            'billingAddress' => $billingAddress,
            'shippingAddress' => $shippingAddress
        ];
        $this->view->assignMultiple($assignArguments);
    }

    /**
     * Action Clear Cart
     */
    public function clearAction()
    {
        $this->cart = $this->cartUtility->getNewCart($this->pluginSettings);

        $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);

        $this->redirect('show');
    }

    /**
     * Action Update Cart
     */
    public function updateAction()
    {
        if (!$this->request->hasArgument('quantities')) {
            $this->redirect('show');
        }

        $updateQuantities = $this->request->getArgument('quantities');

        if (!is_array($updateQuantities)) {
            $this->redirect('show');
        }

        $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

        foreach ($updateQuantities as $productId => $quantity) {
            $cartProduct = $this->cart->getProductById($productId);
            if ($cartProduct) {
                $availabilityResponse = $this->stockUtility->checkAvailability($this->request, $cartProduct, $this->cart, 'update');
                if ($availabilityResponse->isAvailable()) {
                    if (is_array($quantity)) {
                        $cartProduct->changeQuantities($quantity);
                    } else {
                        $cartProduct->changeQuantity($quantity);
                    }
                } else {
                    foreach ($availabilityResponse->getMessages() as $message) {
                        $this->addFlashMessage(
                            $message->getMessage(),
                            $message->getTitle(),
                            $message->getSeverity(),
                            true
                        );
                    }
                }
            }
        }
        $this->cart->reCalc();

        $this->cartUtility->updateService($this->cart, $this->pluginSettings);

        $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);

        $this->redirect('show');
    }
}
