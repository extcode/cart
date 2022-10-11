<?php

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Service;
use Extcode\Cart\Service\SessionHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;

class CartUtility
{
    /**
     * Session Handler
     *
     * @var SessionHandler
     */
    protected $sessionHandler;

    /**
     * Parser Utility
     *
     * @var ParserUtility
     */
    protected $parserUtility;

    /**
     * @param SessionHandler $sessionHandler
     */
    public function injectSessionHandler(
        SessionHandler $sessionHandler
    ) {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @param ParserUtility $parserUtility
     */
    public function injectParserUtility(
        ParserUtility $parserUtility
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
     * @param Request $request
     */
    public function updateCountry(array $cartSettings, array $pluginSettings, Request $request)
    {
        $sessionHandler = GeneralUtility::makeInstance(
            SessionHandler::class
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

        $signalSlotDispatcher = GeneralUtility::makeInstance(
            Dispatcher::class
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

    public function updateService(Cart $cart, $pluginSettings)
    {
        $parserUtility = GeneralUtility::makeInstance(
            ParserUtility::class
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
     * @return Cart
     */
    public function getCartFromSession(array $pluginSettings)
    {
        $cart = $this->sessionHandler->restore($pluginSettings['settings']['cart']['pid']);

        if (!$cart instanceof Cart) {
            $cart = $this->getNewCart($pluginSettings);
            $this->sessionHandler->write($cart, $pluginSettings['settings']['cart']['pid']);
        }

        return $cart;
    }

    /**
     * Restore cart from session or creates a new one
     *
     * @param Cart $cart
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
     * @return Cart
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

        /** @var Cart $cart */
        $cart = GeneralUtility::makeInstance(
            Cart::class,
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
     * @param Cart $cart
     */
    protected function setShipping(array $pluginSettings, Cart $cart)
    {
        $shippings = $this->parserUtility->parseServices('Shipping', $pluginSettings, $cart);

        foreach ($shippings as $shipping) {
            /**
             * Shipping
             * @var Service $shipping
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
     * @param Cart $cart
     */
    protected function setPayment(array $pluginSettings, Cart $cart)
    {
        $payments = $this->parserUtility->parseServices('Payment', $pluginSettings, $cart);

        foreach ($payments as $payment) {
            /**
             * Payment
             * @var Service $payment
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
