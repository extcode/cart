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
 * Cart Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
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
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress
     */
    public function showAction(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem = null,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress = null,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        $this->restoreSession();

        if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['showCartActionAfterCartWasLoaded']) {
            foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['showCartActionAfterCartWasLoaded'] as $funcRef) {
                if ($funcRef) {
                    $params = [
                        'request' => $this->request,
                        'settings' => $this->settings,
                        'cart' => &$this->cart,
                        'orderItem' => &$orderItem,
                        'billingAddress' => &$billingAddress,
                        'shippingAddess' => &$shippingAddress,
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

        if ($orderItem == null) {
            $orderItem = $this->objectManager->get(
                \Extcode\Cart\Domain\Model\Order\Item::class
            );
        }
        if ($billingAddress == null) {
            $billingAddress = $this->objectManager->get(
                \Extcode\Cart\Domain\Model\Order\Address::class
            );
        }
        if ($shippingAddress == null) {
            $shippingAddress = $this->objectManager->get(
                \Extcode\Cart\Domain\Model\Order\Address::class
            );
        }

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

        $this->updateService();

        $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);

        $this->redirect('show');
    }
}
