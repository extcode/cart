<?php

namespace Extcode\Cart\Domain\Finisher\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

class EmailFinisher extends \Extcode\Cart\Domain\Finisher\AbstractFinisher
{
    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    public function executeInternal()
    {
        $this->cart = $this->finisherContext->getCart();
        $orderItem = $this->finisherContext->getOrderItem();

        $paymentCountry = $orderItem->getPayment()->getServiceCountry();
        $paymentId = $orderItem->getPayment()->getServiceId();

        if ($paymentCountry) {
            $serviceSettings = $this->settings['payments'][$paymentCountry]['options'][$paymentId];
        } else {
            $serviceSettings = $this->settings['payments']['options'][$paymentId];
        }

        if (intval($serviceSettings['preventBuyerEmail']) != 1) {
            $this->sendBuyerMail($orderItem);
        }
        if (intval($serviceSettings['preventSellerEmail']) != 1) {
            $this->sendSellerMail($orderItem);
        }
    }

    /**
     * Send a Mail to Buyer
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     */
    protected function sendBuyerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem
    ) {
        $mailHandler = GeneralUtility::makeInstance(
            \Extcode\Cart\Service\MailHandler::class
        );
        $mailHandler->setCart($this->cart);
        $mailHandler->sendBuyerMail($orderItem);
    }

    /**
     * Send a Mail to Seller
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem
     */
    protected function sendSellerMail(
        \Extcode\Cart\Domain\Model\Order\Item $orderItem
    ) {
        $mailHandler = GeneralUtility::makeInstance(
            \Extcode\Cart\Service\MailHandler::class
        );
        $mailHandler->setCart($this->cart);
        $mailHandler->sendSellerMail($orderItem);
    }
}
