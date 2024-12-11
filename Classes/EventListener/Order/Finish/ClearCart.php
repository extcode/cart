<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Finish;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;
use Extcode\Cart\Event\Order\EventInterface;
use Extcode\Cart\Service\PaymentMethodsServiceInterface;
use Extcode\Cart\Service\SessionHandler;
use Extcode\Cart\Utility\CartUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ClearCart
{
    public function __construct(
        protected readonly CartUtility $cartUtility,
        protected readonly PaymentMethodsServiceInterface $paymentMethodsService,
        protected readonly SessionHandler $sessionHandler
    ) {}

    public function __invoke(EventInterface $event): void
    {
        $cart = $event->getCart();
        $settings = $event->getSettings();

        $paymentId = $cart->getPayment()->getId();
        $paymentSettings = $this->paymentMethodsService->getConfigurationsForType('payments', $cart->getBillingCountry());

        if ((int)($paymentSettings['options'][$paymentId]['preventClearCart'] ?? 0) != 1) {
            $cartPid = $settings['settings']['cart']['pid'];

            $this->sessionHandler->writeCart(
                $cartPid,
                $this->cartUtility->getNewCart($settings)
            );
            $this->sessionHandler->writeAddress(
                'billing_address_' . $cartPid,
                GeneralUtility::makeInstance(BillingAddress::class)
            );
            $this->sessionHandler->writeAddress(
                'shipping_address_' . $cartPid,
                GeneralUtility::makeInstance(ShippingAddress::class)
            );
        }
    }
}
