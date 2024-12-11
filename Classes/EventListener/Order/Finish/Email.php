<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Finish;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Event\Order\EventInterface;
use Extcode\Cart\Service\MailHandler;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Email
{
    protected Cart $cart;

    public function __invoke(EventInterface $event): void
    {
        $this->cart = $event->getCart();
        $orderItem = $event->getOrderItem();
        $settings = $event->getSettings();

        $paymentCountry = $orderItem->getPayment()->getServiceCountry();
        $paymentId = $orderItem->getPayment()->getServiceId();

        if ($paymentCountry) {
            $serviceSettings = $settings['payments']['countries'][$paymentCountry]['options'][$paymentId];
        } else {
            $serviceSettings = $settings['payments']['options'][$paymentId];
        }

        if ((int)($serviceSettings['preventBuyerEmail'] ?? 0) !== 1) {
            $this->sendBuyerMail($orderItem);
        }
        if ((int)($serviceSettings['preventSellerEmail'] ?? 0) !== 1) {
            $this->sendSellerMail($orderItem);
        }
    }

    /**
     * send an email to buyer
     */
    protected function sendBuyerMail(Item $orderItem): void
    {
        $mailHandler = GeneralUtility::makeInstance(
            MailHandler::class
        );
        $mailHandler->setCart($this->cart);
        $mailHandler->sendBuyerMail($orderItem);
    }

    /**
     * send an email to seller
     */
    protected function sendSellerMail(Item $orderItem): void
    {
        $mailHandler = GeneralUtility::makeInstance(
            MailHandler::class
        );
        $mailHandler->setCart($this->cart);
        $mailHandler->sendSellerMail($orderItem);
    }
}
