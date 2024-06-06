<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Service\PaymentMethodsServiceInterface;
use Extcode\Cart\Service\SessionHandler;
use Extcode\Cart\Service\ShippingMethodsServiceInterface;
use Extcode\Cart\Utility\CartUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    protected SessionHandler $sessionHandler;

    protected CartUtility $cartUtility;

    protected PaymentMethodsServiceInterface $paymentMethodService;
    protected ShippingMethodsServiceInterface $shippingMethodService;

    protected array $configurations;

    protected Cart $cart;

    protected array $payments = [];
    protected array $shippings = [];
    protected array $specials = [];

    public function injectSessionHandler(SessionHandler $sessionHandler): void
    {
        $this->sessionHandler = $sessionHandler;
    }

    public function injectCartUtility(CartUtility $cartUtility): void
    {
        $this->cartUtility = $cartUtility;
    }

    public function injectPaymentMethodService(PaymentMethodsServiceInterface $paymentMethodsService): void
    {
        $this->paymentMethodService = $paymentMethodsService;
    }

    public function injectShippingMethodService(ShippingMethodsServiceInterface $shippingMethodsService): void
    {
        $this->shippingMethodService = $shippingMethodsService;
    }

    public function initializeAction(): void
    {
        $this->configurations = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
        );

        $this->settings['addToCartByAjax'] = isset($this->settings['addToCartByAjax']) ? (int)$this->settings['addToCartByAjax'] : 0;
    }

    protected function parseServices(): void
    {
        $this->payments = $this->paymentMethodService->getPaymentMethods($this->cart);
        $this->shippings = $this->shippingMethodService->getShippingMethods($this->cart);

        // parse all specials
        //        $this->specials = $this->parserUtility->parseServices(
        //            'Special',
        //            $this->configurations,
        //            $this->cart
        //        );
    }

    public function parseServicesAndAssignToView(): void
    {
        $this->parseServices();

        $this->view->assignMultiple(
            [
                'shippings' => $this->shippings,
                'payments' => $this->payments,
                'specials' => $this->specials,
            ]
        );
    }

    protected function restoreSession(): void
    {
        $cart = $this->sessionHandler->restoreCart($this->settings['cart']['pid']);

        if ($cart instanceof Cart) {
            $this->cart = $cart;
            return;
        }

        $this->cart = $this->cartUtility->getNewCart($this->configurations);
        $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);
    }
}
