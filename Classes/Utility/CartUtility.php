<?php

namespace Extcode\Cart\Utility;

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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Cart Utility
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CartUtility
{
    /**
     * Object Manager
     *
     * @var \TYPO3\CMS\Extbase\Object\ObjectManager
     */
    protected $objectManager;

    /**
     * Session Handler
     *
     * @var \Extcode\Cart\Service\SessionHandler
     */
    protected $sessionHandler;

    /**
     * Parser Utility
     *
     * @var \Extcode\Cart\Utility\ParserUtility
     */
    protected $parserUtility;

    /**
     * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
     */
    public function injectObjectManager(
        \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    /**
     * @param \Extcode\Cart\Service\SessionHandler $sessionHandler
     */
    public function injectSessionHandler(
        \Extcode\Cart\Service\SessionHandler $sessionHandler
    ) {
        $this->sessionHandler = $sessionHandler;
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
     * @param array $services
     * @param int $serviceId
     *
     * @return mixed
     */
    public function getServiceById($services, $serviceId)
    {
        foreach ($services as $service) {
            if ($service->getId() == $serviceId) {
                return $service;
            }
        }

        return false;
    }

    /**
     * Get Order Number
     *
     * @param array $pluginSettings TypoScript Plugin Settings
     *
     * @return string
     */
    protected function getOrderNumber(array $pluginSettings)
    {
        $cObjRenderer = GeneralUtility::makeInstance(
            \TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer::class
        );

        $typoScriptService = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Service\TypoScriptService::class
        );
        $pluginTypoScriptSettings = $typoScriptService->convertPlainArrayToTypoScriptArray($pluginSettings);

        $registry = GeneralUtility::makeInstance(
            \TYPO3\CMS\Core\Registry::class
        );

        $registryName = 'lastInvoice_' . $pluginSettings['settings']['cart']['pid'];
        $orderNumber = $registry->get('tx_cart', $registryName);

        $orderNumber = $orderNumber ? $orderNumber + 1 : 1;

        $registry->set('tx_cart', $registryName, $orderNumber);

        $cObjRenderer->start(['orderNumber' => $orderNumber]);
        $orderNumber = $cObjRenderer->
        cObjGetSingle($pluginTypoScriptSettings['orderNumber'], $pluginTypoScriptSettings['orderNumber.']);

        return $orderNumber;
    }

    /**
     * @param string $table
     * @param $databaseFields
     *
     * @return string
     */
    protected function getCartProductDataSelect($table, $databaseFields)
    {
        $select = '';

        foreach ($databaseFields as $databaseFieldKey => $databaseFieldValue) {
            if ($databaseFieldValue != '' && is_string($databaseFieldValue)) {
                if ($databaseFieldValue != '{$plugin.tx_cart.db.' . $databaseFieldKey . '}') {
                    $select .= ', ' . $table . '.' . $databaseFieldValue;
                }
            }
        }

        if ($databaseFields['variants'] != '' && $databaseFields['variants'] != '{$plugin.tx_cart.db.variants}') {
            $select .= ', ' . $table . '.' . $databaseFields['variants'];
        }

        if ($databaseFields['additional.']) {
            foreach ($databaseFields['additional.'] as $additional) {
                if ($additional['field']) {
                    $select .= ', ' . $table . '.' . $additional['field'];
                }
            }
        }

        return $select;
    }

    /**
     * @param array $cartSettings
     * @param array $pluginSettings
     * @param \TYPO3\CMS\Extbase\Mvc\Request $request
     */
    public function updateCountry(array $cartSettings, array $pluginSettings, \TYPO3\CMS\Extbase\Mvc\Request $request)
    {
        $sessionHandler = $this->objectManager->get(
            \Extcode\Cart\Service\SessionHandler::class
        );
        $cart = $sessionHandler->restore($cartSettings['pid']);

        $billingCountry = $cart->getBillingCountry();

        if ($request->hasArgument('billing_country')) {
            $billingCountry = $request->getArgument('billing_country');
        }

        if ($request->hasArgument('shipping_same_as_billing')) {
            $shippingSameAsBilling = $request->getArgument('shipping_same_as_billing') === 'true';
        }

        if ($request->hasArgument('shipping_country')) {
            $shippingCountry = $request->getArgument('shipping_country');
        }

        $data = [
            'cart' => $cart,
            'billingCountry' => $billingCountry,
            'shippingCountry' => $shippingCountry,
            'shippingSameAsBilling' => $shippingSameAsBilling,
        ];

        $signalSlotDispatcher = $this->objectManager->get(
            \TYPO3\CMS\Extbase\SignalSlot\Dispatcher::class
        );
        $signalSlotDispatcher->dispatch(
            __CLASS__,
            __FUNCTION__,
            $data
        );

        $cart->setBillingCountry($billingCountry);
        $cart->setShippingSameAsBilling($shippingSameAsBilling);
        $cart->setShippingCountry($shippingCountry);

        $sessionHandler->write($cart, $cartSettings['pid']);
    }

    public function updateService(\Extcode\Cart\Domain\Model\Cart\Cart $cart, $pluginSettings)
    {
        $parserUtility = $this->objectManager->get(
            \Extcode\Cart\Utility\ParserUtility::class
        );

        $cart->getPayment()->setCart($cart);
        if (!$cart->getPayment()->isAvailable($cart->getGross())) {
            $payments = $parserUtility->parseServices('Payment', $pluginSettings, $cart);
            $fallBackId = $cart->getPayment()->getFallBackId();
            if ($fallBackId) {
                $payment = $this->getServiceById($payments, $fallBackId);
                $cart->setPayment($payment);
            }
        }

        $cart->getShipping()->setCart($cart);
        if (!$cart->getShipping()->isAvailable($cart->getGross())) {
            $shippings = $parserUtility->parseServices('Shipping', $pluginSettings, $cart);
            $fallBackId = $cart->getShipping()->getFallBackId();
            if ($fallBackId) {
                $shipping = $this->getServiceById($shippings, $fallBackId);
                $cart->setShipping($shipping);
            }
        }
    }

    /**
     * @var array $pluginSettings
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Cart
     */
    public function getCartFromSession(array $pluginSettings)
    {
        $cart = $this->sessionHandler->restore($pluginSettings['settings']['cart']['pid']);

        if (!$cart instanceof \Extcode\Cart\Domain\Model\Cart\Cart) {
            $cart = $this->getNewCart($pluginSettings);
            $this->sessionHandler->write($cart, $pluginSettings['settings']['cart']['pid']);
        }

        return $cart;
    }

    /**
     * Restore cart from session or creates a new one
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @param array $cartSettings
     */
    public function writeCartToSession($cart, $cartSettings)
    {
        $this->sessionHandler->write($cart, $cartSettings['cart']['pid']);
    }

    /**
     * Creates a new cart
     *
     * @param array $pluginSettings TypoScript Plugin Settings
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Cart
     */
    public function getNewCart(array $pluginSettings)
    {
        $isNetCart = intval($pluginSettings['settings']['cart']['isNetCart']) == 0 ? false : true;

        $defaultCurrency = [];
        $defaultCurrencyNum = $pluginSettings['settings']['currencies']['default'];
        if ($pluginSettings['settings']['currencies'][$defaultCurrencyNum]) {
            $defaultCurrency = $pluginSettings['settings']['currencies'][$defaultCurrencyNum];
        }

        $defaultCountry  = $pluginSettings['settings']['defaultCountry'];

        $taxClasses = $this->parserUtility->parseTaxClasses($pluginSettings, $defaultCountry);

        /** @var \Extcode\Cart\Domain\Model\Cart\Cart $cart */
        $cart = $this->objectManager->get(
            \Extcode\Cart\Domain\Model\Cart\Cart::class,
            $taxClasses,
            $isNetCart,
            $defaultCurrency['code'],
            $defaultCurrency['sign'],
            $defaultCurrency['translation']
        );

        if ($defaultCountry) {
            $cart->setBillingCountry($defaultCountry);
            $cart->setShippingCountry($defaultCountry);
        }

        $this->setShipping($pluginSettings, $cart);

        $this->setPayment($pluginSettings, $cart);

        return $cart;
    }

    /**
     * @param array $pluginSettings
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    protected function setShipping(array $pluginSettings, \Extcode\Cart\Domain\Model\Cart\Cart $cart)
    {
        $shippings = $this->parserUtility->parseServices('Shipping', $pluginSettings, $cart);

        foreach ($shippings as $shipping) {
            /**
             * Shipping
             * @var \Extcode\Cart\Domain\Model\Cart\Service $shipping
             */
            if ($shipping->isPreset()) {
                if (!$shipping->isAvailable($cart->getGross())) {
                    $fallBackId = $shipping->getFallBackId();
                    $shipping = $this->getServiceById($shippings, $fallBackId);
                }
                $cart->setShipping($shipping);
                break;
            }
        }
    }

    /**
     * @param array $pluginSettings
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     */
    protected function setPayment(array $pluginSettings, \Extcode\Cart\Domain\Model\Cart\Cart $cart)
    {
        $payments = $this->parserUtility->parseServices('Payment', $pluginSettings, $cart);

        foreach ($payments as $payment) {
            /**
             * Payment
             * @var \Extcode\Cart\Domain\Model\Cart\Service $payment
             */
            if ($payment->isPreset()) {
                if (!$payment->isAvailable($cart->getGross())) {
                    $fallBackId = $payment->getFallBackId();
                    $payment = $this->getServiceById($payments, $fallBackId);
                }
                $cart->setPayment($payment);
                break;
            }
        }
    }
}
