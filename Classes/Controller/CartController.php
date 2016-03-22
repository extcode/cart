<?php

namespace Extcode\Cart\Controller;

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

use \TYPO3\CMS\Core\Utility;

/**
 * Cart Controller
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CartController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * Session Handler
     *
     * @var \Extcode\Cart\Service\SessionHandler
     * @inject
     */
    protected $sessionHandler;

    /**
     * @var \Extcode\Cart\Domain\Repository\Product\CouponRepository
     * @inject
     */
    protected $couponRepository;

    /**
     * Cart Utility
     *
     * @var \Extcode\Cart\Utility\CartUtility
     * @inject
     */
    protected $cartUtility;

    /**
     * Order Utility
     *
     * @var \Extcode\Cart\Utility\OrderUtility
     * @inject
     */
    protected $orderUtility;

    /**
     * Parser Utility
     *
     * @var \Extcode\Cart\Utility\ParserUtility
     * @inject
     */
    protected $parserUtility;

    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    /**
     * GpValues
     *
     * @var array
     */
    protected $gpValues = array();

    /**
     * TaxClasses
     *
     * @var array
     */
    protected $taxClasses = array();

    /**
     * Shippings
     *
     * @var array
     */
    protected $shippings = array();

    /**
     * Payments
     *
     * @var array
     */
    protected $payments = array();

    /**
     * Specials
     *
     * @var array
     */
    protected $specials = array();

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * Action initialize
     *
     * @return void
     */
    public function initializeAction()
    {
        $this->pluginSettings =
            $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );

        $this->pageId = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');

        if (TYPO3_MODE === 'BE') {
            $frameworkConfiguration = $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );
            $persistenceConfiguration = array('persistence' => array('storagePid' => $this->pageId));
            $this->configurationManager->setConfiguration(
                array_merge($frameworkConfiguration, $persistenceConfiguration)
            );
        }

        $this->piVars = $this->request->getArguments();
    }


    protected function initializeOrderAction()
    {
        /*
        if ($this->request->hasArgument('shipping_same_as_billing')) {
            $this->arguments['orderItem']
                ->getPropertyMappingConfiguration()
                ->skipProperties('shippingAddress');
        }
        */
    }

    /**
     * Action Show Cart
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem OrderItem
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping
     *
     * @return void
     */
    public function showCartAction(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem = null,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress = null,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        $this->view->assign('cart', $this->cart);

        $this->parseData();

        $assignArguments = array(
            'shippings' => $this->shippings,
            'payments' => $this->payments,
            'specials' => $this->specials
        );
        $this->view->assignMultiple($assignArguments);

        if ($orderItem == null) {
            $orderItem = new \Extcode\Cart\Domain\Model\Order\Item();
        }
        if ($billingAddress == null) {
            $billingAddress = new \Extcode\Cart\Domain\Model\Order\Address();
        }
        if ($shippingAddress == null) {
            $shippingAddress = new \Extcode\Cart\Domain\Model\Order\Address();
        }

        $assignArguments = array(
            'orderItem' => $orderItem,
            'billingAddress' => $billingAddress,
            'shippingAddress' => $shippingAddress
        );
        $this->view->assignMultiple($assignArguments);
    }

    /**
     * Action showMini
     *
     * @return void
     */
    public function showMiniAction()
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);
        $this->view->assign('cart', $this->cart);

        $this->parseData();
    }

    /**
     * Action Clear Cart
     *
     * @return void
     */
    public function clearCartAction()
    {
        $this->cart = $this->cartUtility->getNewCart($this->settings['cart'], $this->pluginSettings);

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);

        $this->redirect('showCart');
    }

    /**
     * Action Update Cart
     *
     * @return void
     */
    public function updateCartAction()
    {
        if ($this->request->hasArgument('quantities')) {
            $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);
            $updateQuantities = $this->request->getArgument('quantities');
            if (is_array($updateQuantities)) {
                foreach ($updateQuantities as $productId => $quantity) {
                    $product = $this->cart->getProductById($productId);
                    if ($product) {
                        if (ctype_digit($quantity)) {
                            $quantity = intval($quantity);
                            $product->changeQuantity(intval($quantity));
                        } elseif (is_array($quantity)) {
                            $product->changeVariantsQuantity($quantity);
                        }
                    }
                }
                $this->cart->reCalc();
            }
            $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);
        }
        $this->redirect('showCart');
    }

    /**
     * Action Add Product
     *
     * @return void
     */
    public function addProductAction()
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        // TODO: Check if the function call is necessary.
        $this->parseData();

        $products = $this->cartUtility->getProductsFromRequest(
            $this->pluginSettings,
            $this->request,
            $this->cart->getTaxClasses()
        );

        foreach ($products as $product) {
            $this->cart->addProduct($product);
        }

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);

        $this->redirect('showCart');
    }

    /**
     * Action Add Coupon
     *
     * @return void
     */
    public function addCouponAction()
    {
        if ($this->request->hasArgument('couponCode')) {
            $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

            $couponCode = $this->request->getArgument('couponCode');
            $coupon = $this->couponRepository->findOneByCode($couponCode);
            if ($coupon) {
                $couponWasAdded = $this->cart->addCoupon($coupon);
            }

            $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);
        }

        $this->redirect('showCart');
    }

    /**
     * Action removeProduct
     *
     * @return void
     */
    public function removeProductAction()
    {
        if ($this->request->hasArgument('product')) {
            $this->cart = $this->sessionHandler->restoreFromSession($this->settings['cart']['pid']);
            $this->cart->removeProductById($this->request->getArgument('product'));

            $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);
        }
        $this->redirect('showCart');
    }

    /**
     * Action setShipping
     *
     * @param int $shippingId ShippingId
     *
     * @return void
     */
    public function setShippingAction($shippingId)
    {
        $cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        $this->shippings = $this->parserUtility->parseServices('Shipping', $this->pluginSettings, $cart);

        $shipping = $this->shippings[$shippingId];

        if ($shipping) {
            $cart->setShipping($shipping);
        }

        $this->sessionHandler->writeToSession($cart, $this->settings['cart']['pid']);

        $this->redirect('showCart');
    }

    /**
     * Action setPayment
     *
     * @param int $paymentId PaymentId
     *
     * @return void
     */
    public function setPaymentAction($paymentId)
    {
        $cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        $this->payments = $this->parserUtility->parseServices('Payment', $this->pluginSettings, $cart);

        $payment = $this->payments[$paymentId];

        if ($payment) {
            $cart->setPayment($payment);
        }

        $this->sessionHandler->writeToSession($cart, $this->settings['cart']['pid']);

        $this->redirect('showCart');
    }

    /**
     * Action order Cart
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress
     *
     * @ignorevalidation $shippingAddress
     *
     * @return void
     */
    public function orderCartAction(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);
        $this->parseData();

        $this->orderUtility->checkStock($this->cart);

        if ($this->request->hasArgument('shipping_same_as_billing')) {
            $isShippingAddressSameAsBilling = $this->request->getArgument('shipping_same_as_billing');

            if ($isShippingAddressSameAsBilling == 'true') {
                $shippingAddress = null;
                $orderItem->removeShippingAddress();
            }
        }

        $this->orderUtility->saveOrderItem(
            $this->pluginSettings,
            $this->cart,
            $orderItem,
            $billingAddress,
            $shippingAddress
        );

        $this->orderUtility->handleStock($this->cart);

        $this->orderUtility->handlePayment($orderItem, $this->cart);

        $this->sendMails($orderItem, $billingAddress, $shippingAddress);

        $paymentId = $this->cart->getPayment()->getId();
        if (intval($this->pluginSettings['payments']['options'][$paymentId]['preventClearCart']) != 1) {
            $this->cart = $this->cartUtility->getNewCart($this->settings['cart'], $this->pluginSettings);
        }

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);
    }

    /**
     * Send Mails
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     *
     * @return void
     */
    protected function sendMails(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        $paymentId = $this->cart->getPayment()->getId();
        if (intval($this->pluginSettings['payments']['options'][$paymentId]['preventBuyerEmail']) != 1) {
            $this->sendBuyerMail($orderItem, $billingAddress, $shippingAddress);
        }
        if (intval($this->pluginSettings['payments']['options'][$paymentId]['preventSellerEmail']) != 1) {
            $this->sendSellerMail($orderItem, $billingAddress, $shippingAddress);
        }
    }

    /**
     * Send a Mail to Buyer
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     *
     * @return void
     */
    protected function sendBuyerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        $mailHandler = $this->objectManager->get('Extcode\Cart\Service\MailHandler');
        $mailHandler->setCart($this->cart);
        $mailHandler->sendBuyerMail($orderItem, $billingAddress, $shippingAddress);
    }

    /**
     * Send a Mail to Seller
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     *
     * @return void
     */
    protected function sendSellerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        $mailHandler = $this->objectManager->get('Extcode\Cart\Service\MailHandler');
        $mailHandler->setCart($this->cart);
        $mailHandler->sendSellerMail($orderItem, $billingAddress, $shippingAddress);
    }

    /**
     * Parse Data
     *
     * @return void
     */
    protected function parseData()
    {
        // parse all shippings
        $this->shippings = $this->parserUtility->parseServices('Shipping', $this->pluginSettings, $this->cart);

        // parse all payments
        $this->payments = $this->parserUtility->parseServices('Payment', $this->pluginSettings, $this->cart);

        // parse all specials
        $this->specials = $this->parserUtility->parseServices('Special', $this->pluginSettings, $this->cart);
    }

}