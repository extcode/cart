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

use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Configuration\Exception;
use \TYPO3\CMS\Extbase\Mvc\Request;

/**
 * Cart Utility
 *
 * @package cart
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
     * @param \Extcode\Cart\Utility\ParserUtility $parserUtility
     */
    public function injectParserUtility(
        \Extcode\Cart\Utility\ParserUtility $parserUtility
    ) {
        $this->parserUtility = $parserUtility;
    }

    /**
     * @param \Extcode\Cart\Service\SessionHandler $sessionHandler
     */
    public function injectSessionHandler(\Extcode\Cart\Service\SessionHandler $sessionHandler)
    {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * Restore cart from session or creates a new one
     *
     * @param array $cartSettings
     * @param array $pluginSettings
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Cart
     */
    public function getCartFromSession(array $cartSettings, array $pluginSettings)
    {
        $cart = $this->sessionHandler->restoreFromSession($cartSettings['pid']);

        if (!$cart) {
            $cart = $this->getNewCart($cartSettings, $pluginSettings);
        }

        return $cart;
    }

    /**
     * Restore cart from session or creates a new one
     *
     * @param \Extcode\Cart\Domain\Model\Cart\Cart $cart
     * @param array $cartSettings
     *
     * @return void
     */
    public function writeCartToSession($cart, $cartSettings)
    {
        $this->sessionHandler->writeToSession($cart, $cartSettings['cart']['pid']);
    }

    /**
     * Creates a new cart
     *
     * @param array $cartSettings TypoScript Cart Settings
     * @param array $pluginSettings TypoScript Plugin Settings
     *
     * @return \Extcode\Cart\Domain\Model\Cart\Cart
     */
    public function getNewCart(array $cartSettings, array $pluginSettings)
    {
        $isNetCart = intval($cartSettings['isNetCart']) == 0 ? false : true;

        $taxClasses = $this->parserUtility->parseTaxClasses($pluginSettings);

        $cart = new \Extcode\Cart\Domain\Model\Cart\Cart($taxClasses, $isNetCart);

        $defaultCountry = $pluginSettings['settings']['defaultCountry'];
        if ($defaultCountry) {
            $cart->setBillingCountry($defaultCountry);
        }

        $shippings = $this->parserUtility->parseServices('Shipping', $pluginSettings, $cart);

        foreach ($shippings as $shipping) {
            /**
             * Shipping
             * @var \Extcode\Cart\Domain\Model\Shipping $shipping
             */
            if ($shipping->getIsPreset()) {
                if (!$shipping->isAvailable($cart->getGross())) {
                    $fallBackId = $shipping->getFallBackId();
                    $shipping = $this->getServiceById($shippings, $fallBackId);
                }
                $cart->setShipping($shipping);
                break;
            }
        }

        $payments = $this->parserUtility->parseServices('Payment', $pluginSettings, $cart);

        foreach ($payments as $payment) {
            /**
             * Payment
             * @var \Extcode\Cart\Domain\Model\Payment $payment
             */
            if ($payment->getIsPreset()) {
                if (!$payment->isAvailable($cart->getGross())) {
                    $fallBackId = $payment->getFallBackId();
                    $payment = $this->getServiceById($payments, $fallBackId);
                }
                $cart->setPayment($payment);
                break;
            }
        }

        return $cart;
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
        $select = "";

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
     *
     * @return void
     */
    public function updateCountry(array $cartSettings, array $pluginSettings, $request)
    {
        $cart = $this->getCartFromSession($cartSettings, $pluginSettings);

        $billingCountry = $cart->getBillingCountry();

        if ($request->hasArgument('billing_country')) {
            $billingCountry = $request->getArgument('billing_country');
        }


        if ($request->hasArgument('shipping_country')) {
            $shippingCountry = $request->getArgument('shipping_country');
        }

        $data = [
            'cart' => $cart,
            'billingCountry' => $billingCountry,
            'shippingCountry' => $shippingCountry,
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
        $cart->setShippingCountry($shippingCountry);

        $this->sessionHandler->writeToSession($cart, $cartSettings['pid']);
    }
}
