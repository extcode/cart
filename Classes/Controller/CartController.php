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

/**
 * Cart Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CartController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * Session Handler
     *
     * @var \Extcode\Cart\Service\SessionHandler
     */
    protected $sessionHandler;

    /**
     * @var \Extcode\Cart\Domain\Repository\Product\CouponRepository
     */
    protected $couponRepository;

    /**
     * Cart Utility
     *
     * @var \Extcode\Cart\Utility\CartUtility
     */
    protected $cartUtility;

    /**
     * Order Utility
     *
     * @var \Extcode\Cart\Utility\OrderUtility
     */
    protected $orderUtility;

    /**
     * Parser Utility
     *
     * @var \Extcode\Cart\Utility\ParserUtility
     */
    protected $parserUtility;

    /**
     * Product Utility
     *
     * @var \Extcode\Cart\Utility\ProductUtility
     */
    protected $productUtility;

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
    protected $gpValues = [];

    /**
     * TaxClasses
     *
     * @var array
     */
    protected $taxClasses = [];

    /**
     * Shippings
     *
     * @var array
     */
    protected $shippings = [];

    /**
     * Payments
     *
     * @var array
     */
    protected $payments = [];

    /**
     * Specials
     *
     * @var array
     */
    protected $specials = [];

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * @param \Extcode\Cart\Service\SessionHandler $sessionHandler
     */
    public function injectSessionHandler(\Extcode\Cart\Service\SessionHandler $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @param \Extcode\Cart\Domain\Repository\Product\CouponRepository $couponRepository
     */
    public function injectCouponRepository(
        \Extcode\Cart\Domain\Repository\Product\CouponRepository $couponRepository
    ) {
        $this->couponRepository = $couponRepository;
    }

    /**
     * @param \Extcode\Cart\Utility\CartUtility $cartUtility
     */
    public function injectCartUtility(
        \Extcode\Cart\Utility\CartUtility $cartUtility
    ) {
        $this->cartUtility = $cartUtility;
    }

    /**
     * @param \Extcode\Cart\Utility\OrderUtility $orderUtility
     */
    public function injectOrderUtility(
        \Extcode\Cart\Utility\OrderUtility $orderUtility
    ) {
        $this->orderUtility = $orderUtility;
    }

    /**
     * @param \Extcode\Cart\Utility\ParserUtility $parserUtility
     */
    public function injectParserUtility(
        \Extcode\Cart\Utility\ParserUtility $parserUtility
    ) {
        $this->parserUtility = $parserUtility;
    }

    /**
     * @param \Extcode\Cart\Utility\ProductUtility $productUtility
     */
    public function injectProductUtility(
        \Extcode\Cart\Utility\ProductUtility $productUtility
    ) {
        $this->productUtility = $productUtility;
    }

    /**
     * @return string
     */
    protected function getErrorFlashMessage()
    {
        $getValidationResults = $this->arguments->getValidationResults();

        if ($getValidationResults->hasErrors()) {
            $errorMsg = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                'tx_cart.error.validation',
                $this->extensionName
            );

            return $errorMsg;
        }

        $errorMsg = parent::getErrorFlashMessage();

        return $errorMsg;
    }

    /**
     * Action initialize
     */
    public function initializeAction()
    {
        $this->pluginSettings = $this->configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        if (TYPO3_MODE === 'BE') {
            $pageId = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');

            $frameworkConf = $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );
            $persistenceConf = ['persistence' => ['storagePid' => $pageId]];
            $this->configurationManager->setConfiguration(
                array_merge($frameworkConf, $persistenceConf)
            );
        }
    }

    /**
     * Action Show Cart
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress
     */
    public function showCartAction(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem = null,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress = null,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

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
     * Action showMiniCart
     */
    public function showMiniCartAction()
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);
        $this->view->assign('cart', $this->cart);
    }

    /**
     * Action Clear Cart
     */
    public function clearCartAction()
    {
        $this->cart = $this->cartUtility->getNewCart($this->settings['cart'], $this->pluginSettings);

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);

        $this->redirect('showCart');
    }

    /**
     *
     */
    public function updateCountryAction()
    {
        //ToDo check country is allowed by TypoScript

        $this->cartUtility->updateCountry($this->settings['cart'], $this->pluginSettings, $this->request);

        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        $taxClasses = $this->parserUtility->parseTaxClasses($this->pluginSettings, $this->cart->getBillingCountry());

        $this->cart->setTaxClasses($taxClasses);
        $this->cart->reCalc();

        $this->parseData();

        $paymentId = $this->cart->getPayment()->getId();
        if ($this->payments[$paymentId]) {
            $payment = $this->payments[$paymentId];
            $this->cart->setPayment($payment);
        } else {
            foreach ($this->payments as $payment) {
                if ($payment->getIsPreset()) {
                    $this->cart->setPayment($payment);
                }
            }
        }
        $shippingId = $this->cart->getShipping()->getId();
        if ($this->shippings[$shippingId]) {
            $shipping = $this->shippings[$shippingId];
            $this->cart->setShipping($shipping);
        } else {
            foreach ($this->shippings as $shipping) {
                if ($shipping->getIsPreset()) {
                    $this->cart->setShipping($shipping);
                }
            }
        }

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);

        $this->updateService();

        $this->view->assign('cart', $this->cart);

        $assignArguments = [
            'shippings' => $this->shippings,
            'payments' => $this->payments,
            'specials' => $this->specials
        ];
        $this->view->assignMultiple($assignArguments);
    }

    /**
     *
     */
    public function editCurrencyAction()
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);
        $this->view->assign('cart', $this->cart);
    }

    /**
     *
     */
    public function updateCurrencyAction()
    {
        $this->cartUtility->updateCurrency($this->settings['cart'], $this->pluginSettings, $this->request);

        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);

        $this->updateService();

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

    /**
     * Action Update Cart
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

            $this->updateService();

            $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);
        }
        $this->redirect('showCart');
    }

    /**
     * Action Add Product
     *
     * @return string
     */
    public function addProductAction()
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        $products = $this->productUtility->getProductsFromRequest(
            $this->pluginSettings,
            $this->request,
            $this->cart->getTaxClasses()
        );

        list($products, $errors) = $this->productUtility->checkProductsBeforeAddToCart($this->cart, $products);

        $quantity = $this->addProductsToCart($products);

        $this->updateService();

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);

        if (isset($_GET['type'])) {
            $productsChanged = $this->getChangedProducts($products);

            // ToDo: have different response status
            $response = [
                'status' => '200',
                'added' => $quantity,
                'count' => $this->cart->getCount(),
                'net' => $this->cart->getNet(),
                'gross' => $this->cart->getGross(),
                'productsChanged' => $productsChanged,
            ];

            return json_encode($response);
        } else {
            if ($errors) {
                if (is_array($errors)) {
                    foreach ($errors as $error) {
                        if ($error['message']) {
                            $severity = !empty($error['severity']) ? $error['severity'] : $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING;
                            $storeInSession = true;

                            $this->addFlashMessage(
                                $error['message'],
                                '',
                                $severity,
                                $storeInSession
                            );
                        }
                    }
                }
            }

            $this->redirect('showCart');
        }
    }

    protected function updateService()
    {
        $this->parseData();
        if (!$this->cart->getPayment()->isAvailable($this->cart->getGross())) {
            $fallBackId = $this->cart->getPayment()->getFallBackId();
            if ($fallBackId) {
                $payment = $this->cartUtility->getServiceById($this->payments, $fallBackId);
                $this->cart->setPayment($payment);
            }
        }

        if (!$this->cart->getShipping()->isAvailable($this->cart->getGross())) {
            $fallBackId = $this->cart->getShipping()->getFallBackId();
            if ($fallBackId) {
                $shipping = $this->cartUtility->getServiceById($this->shippings, $fallBackId);
                $this->cart->setShipping($shipping);
            }
        }
    }

    /**
     * Action Add Coupon
     */
    public function addCouponAction()
    {
        if ($this->request->hasArgument('couponCode')) {
            $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

            $couponCode = $this->request->getArgument('couponCode');

            /** @var \Extcode\Cart\Domain\Model\Product\Coupon $coupon */
            $coupon = $this->couponRepository->findOneByCode($couponCode);
            if ($coupon && $coupon->getIsAvailable()) {
                $newCartCoupon = $this->objectManager->get(
                    \Extcode\Cart\Domain\Model\Cart\CartCoupon::class,
                    $coupon->getTitle(),
                    $coupon->getCode(),
                    $coupon->getCouponType(),
                    $coupon->getDiscount(),
                    $this->cart->getTaxClass($coupon->getTaxClassId()),
                    $coupon->getCartMinPrice(),
                    $coupon->getIsCombinable()
                );

                $couponWasAdded = $this->cart->addCoupon($newCartCoupon);

                if ($couponWasAdded == 1) {
                    $this->addFlashMessage(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'tx_cart.ok.coupon.added',
                            $this->extensionName
                        ),
                        '',
                        \TYPO3\CMS\Core\Messaging\AbstractMessage::OK,
                        true
                    );
                }
                if ($couponWasAdded == -1) {
                    $this->addFlashMessage(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'tx_cart.error.coupon.already_added',
                            $this->extensionName
                        ),
                        '',
                        \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                        true
                    );
                }
                if ($couponWasAdded == -2) {
                    $this->addFlashMessage(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'tx_cart.error.coupon.not_combinable',
                            $this->extensionName
                        ),
                        '',
                        \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                        true
                    );
                }
            } else {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.error.coupon.not_accepted',
                        $this->extensionName
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                    true
                );
            }

            $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);
        }

        $this->redirect('showCart');
    }

    /**
     * Action Remove Coupon
     */
    public function removeCouponAction()
    {
        if ($this->request->hasArgument('couponCode')) {
            $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);
            $couponCode = $this->request->getArgument('couponCode');
            $couponWasRemoved = $this->cart->removeCoupon($couponCode);

            if ($couponWasRemoved == 1) {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.ok.coupon.removed',
                        $this->extensionName
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::OK,
                    true
                );
            }
            if ($couponWasRemoved == -1) {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.error.coupon.not_found',
                        $this->extensionName
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                    true
                );
            }

            $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);
        }

        $this->redirect('showCart');
    }

    /**
     * Action removeProduct
     */
    public function removeProductAction()
    {
        if ($this->request->hasArgument('product')) {
            $this->cart = $this->sessionHandler->restoreFromSession($this->settings['cart']['pid']);
            $this->cart->removeProductById($this->request->getArgument('product'));

            $this->updateService();

            $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);
        }
        $this->redirect('showCart');
    }

    /**
     * Action setShipping
     *
     * @param int $shippingId
     */
    public function setShippingAction($shippingId)
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        $this->shippings = $this->parserUtility->parseServices('Shipping', $this->pluginSettings, $this->cart);

        $shipping = $this->shippings[$shippingId];

        if ($shipping) {
            if ($shipping->isAvailable($this->cart->getGross())) {
                $this->cart->setShipping($shipping);
            } else {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.controller.cart.action.set_shipping.not_available',
                        $this->extensionName
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR,
                    true
                );
            }
        }

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);

        if (isset($_GET['type'])) {
            $this->view->assign('cart', $this->cart);

            $this->parseData();
            $assignArguments = [
                'shippings' => $this->shippings,
                'payments' => $this->payments,
                'specials' => $this->specials
            ];
            $this->view->assignMultiple($assignArguments);
        } else {
            $this->redirect('showCart');
        }
    }

    /**
     * Action setPayment
     *
     * @param int $paymentId
     */
    public function setPaymentAction($paymentId)
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        $this->payments = $this->parserUtility->parseServices('Payment', $this->pluginSettings, $this->cart);

        $payment = $this->payments[$paymentId];

        if ($payment) {
            if ($payment->isAvailable($this->cart->getGross())) {
                $this->cart->setPayment($payment);
            } else {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.controller.cart.action.set_payment.not_available',
                        $this->extensionName
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR,
                    true
                );
            }
        }

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);

        if (isset($_GET['type'])) {
            $this->view->assign('cart', $this->cart);

            $this->parseData();
            $assignArguments = [
                'shippings' => $this->shippings,
                'payments' => $this->payments,
                'specials' => $this->specials
            ];
            $this->view->assignMultiple($assignArguments);
        } else {
            $this->redirect('showCart');
        }
    }

    public function initializeOrderCartAction()
    {
        foreach (['orderItem', 'billingAddress', 'shippingAddress'] as $argumentName) {
            if (!$this->arguments->hasArgument($argumentName)) {
                continue;
            }
            if ($this->settings['validation'] &&
                $this->settings['validation'][$argumentName] &&
                $this->settings['validation'][$argumentName]['fields']
            ) {
                $fields = $this->settings['validation'][$argumentName]['fields'];

                foreach ($fields as $propertyName => $validatorConf) {
                    $this->setDynamicValidation(
                        $argumentName,
                        $propertyName,
                        [
                            'validator' => $validatorConf['validator'],
                            'options' => is_array($validatorConf['options'])
                                         ? $validatorConf['options']
                                         : []
                        ]
                    );
                }
            }
        }

        if ($this->arguments->hasArgument('orderItem')) {
            $this->arguments->getArgument('orderItem')
                ->getPropertyMappingConfiguration()
                ->setTargetTypeForSubProperty('additional', 'array');
        }
        if ($this->arguments->hasArgument('billingAddress')) {
            $this->arguments->getArgument('billingAddress')
                ->getPropertyMappingConfiguration()
                ->setTargetTypeForSubProperty('additional', 'array');
        }
        if ($this->arguments->hasArgument('shippingAddress')) {
            $this->arguments->getArgument('shippingAddress')
                ->getPropertyMappingConfiguration()
                ->setTargetTypeForSubProperty('additional', 'array');
        }
    }

    /**
     * Action order Cart
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress
     *
     * @ignorevalidation $shippingAddress
     */
    public function orderCartAction(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem = null,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress = null,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        if (($orderItem == null) || ($billingAddress == null)) {
            $this->redirect('showCart');
        }

        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        if ($this->cart->getCount() == 0) {
            $this->redirect('showCart');
        }

        $this->parseData();

        $this->orderUtility->checkStock($this->cart, $this->pluginSettings);

        $orderItem->setCartPid(intval($GLOBALS['TSFE']->id));

        if ($this->request->hasArgument('shipping_same_as_billing')) {
            $useSameAddress = $this->request->getArgument('shipping_same_as_billing');

            if ($useSameAddress === 'true') {
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

        $this->orderUtility->handleStock($this->cart, $this->pluginSettings);

        $providerUsed = $this->orderUtility->handlePayment($orderItem, $this->cart);

        if (!$providerUsed) {
            $this->orderUtility->autoGenerateDocuments($orderItem, $this->pluginSettings);

            $this->sendMails($orderItem, $billingAddress, $shippingAddress);

            $this->view->assign('cart', $this->cart);
            $this->view->assign('orderItem', $orderItem);
        }

        $paymentId = $this->cart->getPayment()->getId();
        $paymentSettings = $this->parserUtility->getTypePluginSettings($this->pluginSettings, $this->cart, 'payments');

        if (intval($paymentSettings['options'][$paymentId]['preventClearCart']) != 1) {
            $this->cart = $this->cartUtility->getNewCart($this->settings['cart'], $this->pluginSettings);
        }

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);

        if ($paymentSettings['options'][$paymentId] &&
            $paymentSettings['options'][$paymentId]['redirects'] &&
            $paymentSettings['options'][$paymentId]['redirects']['success'] &&
            $paymentSettings['options'][$paymentId]['redirects']['success']['url']
        ) {
            $this->redirectToURI($paymentSettings['options'][$paymentId]['redirects']['success']['url'], 0, 200);
        }
    }

    /**
     * Action order Finish
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     * @ignorevalidation $orderItem
     */
    public function orderFinishedAction(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem
    ) {
        $this->view->assign('orderItem', $orderItem);
    }

    /**
     * Send Mails
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     */
    protected function sendMails(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        $paymentCountry = $orderItem->getPayment()->getServiceCountry();
        $paymentId = $orderItem->getPayment()->getServiceId();

        if ($paymentCountry) {
            $serviceSettings = $this->pluginSettings['payments'][$paymentCountry]['options'][$paymentId];
        } else {
            $serviceSettings = $this->pluginSettings['payments']['options'][$paymentId];
        }

        if (intval($serviceSettings['preventBuyerEmail']) != 1) {
            $this->sendBuyerMail($orderItem, $billingAddress, $shippingAddress);
        }
        if (intval($serviceSettings['preventSellerEmail']) != 1) {
            $this->sendSellerMail($orderItem, $billingAddress, $shippingAddress);
        }
    }

    /**
     * Send a Mail to Buyer
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     */
    protected function sendBuyerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        /* @var \Extcode\Cart\Service\MailHandler $mailHandler*/
        $mailHandler = $this->objectManager->get(
            \Extcode\Cart\Service\MailHandler::class
        );
        $mailHandler->setCart($this->cart);
        $mailHandler->sendBuyerMail($orderItem, $billingAddress, $shippingAddress);
    }

    /**
     * Send a Mail to Seller
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param \Extcode\Cart\Domain\Model\Order\Address $billingAddress Billing Address
     * @param \Extcode\Cart\Domain\Model\Order\Address $shippingAddress Shipping Address
     */
    protected function sendSellerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        /* @var \Extcode\Cart\Service\MailHandler $mailHandler*/
        $mailHandler = $this->objectManager->get(
            \Extcode\Cart\Service\MailHandler::class
        );
        $mailHandler->setCart($this->cart);
        $mailHandler->sendSellerMail($orderItem, $billingAddress, $shippingAddress);
    }

    /**
     * Parse Data
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

    /**
     * Sets the dynamic validation rules.
     *
     * @param string $argumentName
     * @param string $propertyName
     * @param array $validatorConf
     * @throws \TYPO3\CMS\Extbase\Validation\Exception\NoSuchValidatorException
     */
    protected function setDynamicValidation($argumentName, $propertyName, $validatorConf)
    {
        // build custom validation chain
        /** @var \TYPO3\CMS\Extbase\Validation\ValidatorResolver $validatorResolver */
        $validatorResolver = $this->objectManager->get(
            \TYPO3\CMS\Extbase\Validation\ValidatorResolver::class
        );

        if ($validatorConf['validator'] == 'Empty') {
            $validatorConf['validator'] = '\Extcode\Cart\Validation\Validator\EmptyValidator';
        }

        $propertyValidator = $validatorResolver->createValidator(
            $validatorConf['validator'],
            $validatorConf['options']
        );

        if ($argumentName === 'orderItem') {
            /** @var \Extcode\Cart\Domain\Validator\OrderItemValidator $modelValidator */
            $modelValidator = $validatorResolver->createValidator(
                \Extcode\Cart\Domain\Validator\OrderItemValidator::class
            );
        } else {
            /** @var \TYPO3\CMS\Extbase\Validation\Validator\GenericObject $modelValidator */
            $modelValidator = $validatorResolver->createValidator('GenericObject');
        }

        $modelValidator->addPropertyValidator(
            $propertyName,
            $propertyValidator
        );

        /** @var \TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator $conjunctionValidator */
        $conjunctionValidator = $this->arguments->getArgument($argumentName)->getValidator();
        if ($conjunctionValidator === null) {
            $conjunctionValidator = $validatorResolver->createValidator(
                \TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator::class
            );
            $this->arguments->getArgument($argumentName)->setValidator($conjunctionValidator);
        }
        $conjunctionValidator->addValidator($modelValidator);
    }

    /**
     * returns list of changed products
     *
     * @param $products
     *
     * @return array
     */
    protected function getChangedProducts($products)
    {
        $productsChanged = [];

        foreach ($products as $product) {
            if ($product instanceof \Extcode\Cart\Domain\Model\Cart\Product) {
                $productChanged = $this->cart->getProduct($product->getId());
                $productsChanged[$product->getId()] = $productChanged->toArray();
            }
        }
        return $productsChanged;
    }

    /**
     * @param $products
     * @return int
     */
    protected function addProductsToCart($products)
    {
        $quantity = 0;

        foreach ($products as $product) {
            if ($product instanceof \Extcode\Cart\Domain\Model\Cart\Product) {
                $quantity += $product->getQuantity();
                $this->cart->addProduct($product);
            }
        }
        return $quantity;
    }
}
