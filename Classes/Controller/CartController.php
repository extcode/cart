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
 * @package cart
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


        if (TYPO3_MODE === 'BE') {
            $pageId = (int)\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id');

            $frameworkConfiguration = $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );
            $persistenceConfiguration = ['persistence' => ['storagePid' => $pageId]];
            $this->configurationManager->setConfiguration(
                array_merge($frameworkConfiguration, $persistenceConfiguration)
            );
        }
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
     *
     * @return void
     */
    public function showMiniCartAction()
    {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);
        $this->view->assign('cart', $this->cart);
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
     * @return string
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

        $quantity = 0;
        foreach ($products as $product) {
            $quantity += $product->getQuantity();
            $this->cart->addProduct($product);
        }

        $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);

        $productsChanged = [];

        foreach ($products as $product) {
            $productChanged = $this->cart->getProduct($product->getId());
            $productsChanged[$product->getId()] = $productChanged->toArray();
        }

        if (isset($_GET['type'])) {
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
            $this->redirect('showCart');
        }
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
                        $messageTitle = '',
                        $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK,
                        $storeInSession = true
                    );
                }
                if ($couponWasAdded == -1) {
                    $this->addFlashMessage(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'tx_cart.error.coupon.already_added',
                            $this->extensionName
                        ),
                        $messageTitle = '',
                        $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                        $storeInSession = true
                    );
                }
                if ($couponWasAdded == -2) {
                    $this->addFlashMessage(
                        \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                            'tx_cart.error.coupon.not_combinable',
                            $this->extensionName
                        ),
                        $messageTitle = '',
                        $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                        $storeInSession = true
                    );
                }
            } else {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.error.coupon.not_accepted',
                        $this->extensionName
                    ),
                    $messageTitle = '',
                    $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                    $storeInSession = true
                );
            }

            $this->sessionHandler->writeToSession($this->cart, $this->settings['cart']['pid']);
        }

        $this->redirect('showCart');
    }

    /**
     * Action Remove Coupon
     *
     * @return void
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
                    $messageTitle = '',
                    $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::OK,
                    $storeInSession = true
                );
            }
            if ($couponWasRemoved == -1) {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.error.coupon.not_found',
                        $this->extensionName
                    ),
                    $messageTitle = '',
                    $severity = \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                    $storeInSession = true
                );
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

    public function initializeOrderCartAction()
    {
        if ($this->pluginSettings['validation'] &&
            $this->pluginSettings['validation']['orderCartAction'] &&
            $this->pluginSettings['validation']['orderCartAction']['fields']
        ) {
            $fields = $this->pluginSettings['validation']['orderCartAction']['fields'];

            if (array_key_exists('acceptTerms', $fields)) {
                $this->setDynamicValidation('acceptTerms', $fields['acceptTerms']);
            }
            if (array_key_exists('acceptConditions', $fields)) {
                $this->setDynamicValidation('acceptConditions', $fields['acceptConditions']);
            }
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
     *
     * @return void
     */
    public function orderCartAction(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem,
        \Extcode\Cart\Domain\Model\Order\Address $billingAddress,
        \Extcode\Cart\Domain\Model\Order\Address $shippingAddress = null
    ) {
        $this->cart = $this->cartUtility->getCartFromSession($this->settings['cart'], $this->pluginSettings);

        if ($this->cart->getCount() == 0) {
            $this->redirect('showCart');
        }
        $this->parseData();

        $this->orderUtility->checkStock($this->cart);

        $orderItem->setCartPid(intval($GLOBALS['TSFE']->id));

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

        $this->view->assign('cart', $this->cart);
        $this->view->assign('orderItem', $orderItem);

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
     *
     * @return void
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

    /**
     * Sets the dynamic validation rules.
     *
     * @param string $propertyName
     * @param string $propertyValue
     * @throws \TYPO3\CMS\Extbase\Validation\Exception\NoSuchValidatorException
     */
    protected function setDynamicValidation($propertyName, $propertyValue)
    {
        if ($propertyValue == 'true') {
            // build custom validation chain
            /** @var \TYPO3\CMS\Extbase\Validation\ValidatorResolver $validatorResolver */
            $validatorResolver = $this->objectManager->get(
                \TYPO3\CMS\Extbase\Validation\ValidatorResolver::class
            );

            $booleanValidator = $this->objectManager->get(
                \TYPO3\CMS\Extbase\Validation\Validator\BooleanValidator::class,
                [
                    'is' => $propertyValue,
                ]
            );

            /** @var \Extcode\Cart\Domain\Validator\OrderItemValidator $modelValidator */
            $modelValidator = $validatorResolver->createValidator(
                \Extcode\Cart\Domain\Validator\OrderItemValidator::class
            );

            $modelValidator->addPropertyValidator(
                $propertyName,
                $booleanValidator
            );

            /** @var \TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator $baseConjunctionValidator */
            $baseConjunctionValidator = $this->arguments->getArgument('orderItem')->getValidator();
            if ($baseConjunctionValidator === null) {
                $baseConjunctionValidator = $validatorResolver->createValidator(
                    \TYPO3\CMS\Extbase\Validation\Validator\ConjunctionValidator::class
                );
                $this->arguments->getArgument('orderItem')->setValidator($baseConjunctionValidator);
            }
            $baseConjunctionValidator->addValidator($modelValidator);
        }
    }
}