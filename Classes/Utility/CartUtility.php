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
use Extcode\Cart\Event\Cart\UpdateCountryEvent;
use Extcode\Cart\Service\SessionHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Request;
use TYPO3\CMS\Extbase\Utility\DebuggerUtility;

class CartUtility
{
    protected EventDispatcherInterface $eventDispatcher;

    protected SessionHandler $sessionHandler;

    protected ParserUtility $parserUtility;

    public function __construct(
        EventDispatcherInterface $eventDispatcher,
        ParserUtility $parserUtility,
        SessionHandler $sessionHandler
    ) {
        $this->eventDispatcher = $eventDispatcher;
        $this->parserUtility = $parserUtility;
        $this->sessionHandler = $sessionHandler;
    }

    public function getServiceById(array $services, int $serviceId): mixed
    {
        foreach ($services as $service) {
            if ($service->getId() == $serviceId) {
                return $service;
            }
        }

        return false;
    }

    public function updateCountry(array $cartSettings, array $pluginSettings, Request $request): void
    {
        $cart = $this->sessionHandler->restoreCart($cartSettings['pid']);

        $event = new UpdateCountryEvent($cart, $request);
        $this->eventDispatcher->dispatch($event);

        $this->sessionHandler->writeCart($cartSettings['pid'], $event->getCart());
    }

    public function updateService(Cart $cart, $pluginSettings): void
    {
        $parserUtility = GeneralUtility::makeInstance(
            ParserUtility::class
        );
        if ($cart->getPayment()) {
            $cart->getPayment()->setCart($cart);
            if (!$cart->getPayment()->isAvailable()) {
                $payments = $parserUtility->parseServices('Payment', $pluginSettings, $cart);
                $fallBackId = $cart->getPayment()->getFallBackId();
                if ($fallBackId) {
                    $payment = $this->getServiceById($payments, $fallBackId);
                    $cart->setPayment($payment);
                }
            }
        }

        if($cart->getShipping()) {
            $cart->getShipping()->setCart($cart);
            if (!$cart->getShipping()->isAvailable()) {
                $shippings = $parserUtility->parseServices('Shipping', $pluginSettings, $cart);
                $fallBackId = $cart->getShipping()->getFallBackId();
                if ($fallBackId) {
                    $shipping = $this->getServiceById($shippings, $fallBackId);
                    $cart->setShipping($shipping);
                }
            }
        }
    }

    /**
     * creates a new Cart object from plugin settings
     */
    public function getNewCart(array $configurations): Cart
    {
        $isNetCart = false;
        if (isset($configurations['settings']['cart']['isNetCart']) ){
            $isNetCart = !((int)($configurations['settings']['cart']['isNetCart']) === 0);
        }

        $defaultCurrency = [];
        if (isset($configurations['settings']['currencies']['default'])) {
            $defaultCurrencyNum = $configurations['settings']['currencies']['default'];
            if ($configurations['settings']['currencies'][$defaultCurrencyNum]) {
                $defaultCurrency = $configurations['settings']['currencies'][$defaultCurrencyNum];
            }
        }else {
            $defaultCurrency['code'] = "EUR";
            $defaultCurrency['sign'] = "€";
            $defaultCurrency['translation'] = "1.00";
        }
        

        $defaultCountry  = isset($configurations['settings']['defaultCountry'])?$configurations['settings']['defaultCountry']:"de";

        $taxClasses = $this->parserUtility->parseTaxClasses($configurations, $defaultCountry);

        /** @var Cart $cart */
        $cart = GeneralUtility::makeInstance(
            Cart::class,
            $taxClasses,
            $isNetCart,
            $defaultCurrency['code'],
            $defaultCurrency['sign'],
            (float)$defaultCurrency['translation']
        );

        if ($defaultCountry) {
            $cart->setBillingCountry($defaultCountry);
            $cart->setShippingCountry($defaultCountry);
        }

        $this->setShipping($configurations, $cart);

        $this->setPayment($configurations, $cart);

        return $cart;
    }

    protected function setShipping(array $pluginSettings, Cart $cart): void
    {
        $shippings = $this->parserUtility->parseServices('Shipping', $pluginSettings, $cart);

        foreach ($shippings as $shipping) {
            /**
             * Shipping
             * @var Service $shipping
             */
            if ($shipping->isPreset()) {
                if (!$shipping->isAvailable()) {
                    $fallBackId = $shipping->getFallBackId();
                    $shipping = $this->getServiceById($shippings, $fallBackId);
                }
                $cart->setShipping($shipping);
                break;
            }
        }
    }

    protected function setPayment(array $pluginSettings, Cart $cart): void
    {
        $payments = $this->parserUtility->parseServices('Payment', $pluginSettings, $cart);

        foreach ($payments as $payment) {
            /**
             * Payment
             * @var Service $payment
             */
            if ($payment->isPreset()) {
                if (!$payment->isAvailable()) {
                    $fallBackId = $payment->getFallBackId();
                    $payment = $this->getServiceById($payments, $fallBackId);
                }
                $cart->setPayment($payment);
                break;
            }
        }
    }
}
