<?php
declare(strict_types=1);
namespace Extcode\Cart\EventListener\ProcessOrderCreate;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Order\EventInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class Email
{
    /**
     * Cart
     *
     * @var \Extcode\Cart\Domain\Model\Cart\Cart
     */
    protected $cart;

    public function __invoke(EventInterface $event): void
    {
        $this->cart = $event->getCart();
        $orderItem = $event->getOrderItem();
        $settings = $event->getSettings();

        $paymentCountry = $orderItem->getPayment()->getServiceCountry();
        $paymentId = $orderItem->getPayment()->getServiceId();

        if ($paymentCountry) {
            $serviceSettings = $settings['payments'][$paymentCountry]['options'][$paymentId];
        } else {
            $serviceSettings = $settings['payments']['options'][$paymentId];
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
