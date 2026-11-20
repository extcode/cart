<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Finish;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\Loader\PaymentMethodsLoaderInterface;
use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Event\Order\EventInterface;
use Extcode\Cart\Mail\OrderMailSender;
use Extcode\Cart\Service\MailHandler;
use TYPO3\CMS\Core\Configuration\Features;

/**
 * Sends the emails to the buyer and to the seller at the end of an order.
 *
 * With the feature toggle
 * $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['cart.useNewOrderMailSender']
 * the emails are sent by the OrderMailSender, otherwise by the deprecated
 * MailHandler. In cart 13.0 the OrderMailSender is used always.
 */
class Email
{
    public const FEATURE = 'cart.useNewOrderMailSender';

    /**
     * TODO: 13.0.0 Remove the MailHandler and the feature toggle, see Deprecation #845.
     */
    public function __construct(
        private readonly PaymentMethodsLoaderInterface $paymentMethodsLoader,
        private readonly MailHandler $mailHandler,
        private readonly OrderMailSender $orderMailSender,
        private readonly Features $features,
    ) {
    }

    public function __invoke(EventInterface $event): void
    {
        $orderItem = $event->getOrderItem();

        $paymentMethods = $this->paymentMethodsLoader->getPaymentMethods($event->getCart());
        $paymentId = $orderItem->getPayment()->getServiceId();
        $paymentMethod = $paymentMethods[$paymentId] ?? null;

        if (
            method_exists($paymentMethod, 'isBuyerEmailDisabled') === false
            || $paymentMethod->isBuyerEmailDisabled() === false
        ) {
            $this->sendBuyerMail($orderItem, $event->getCart());
        }
        if (
            method_exists($paymentMethod, 'isSellerEmailDisabled') === false
            || $paymentMethod->isSellerEmailDisabled() === false
        ) {
            $this->sendSellerMail($orderItem, $event->getCart());
        }
    }

    protected function sendBuyerMail(Item $orderItem, Cart $cart): void
    {
        if ($this->features->isFeatureEnabled(self::FEATURE)) {
            $this->orderMailSender->sendBuyerMail($orderItem, $cart);

            return;
        }

        $this->mailHandler->setCart($cart);
        $this->mailHandler->sendBuyerMail($orderItem);
    }

    protected function sendSellerMail(Item $orderItem, Cart $cart): void
    {
        if ($this->features->isFeatureEnabled(self::FEATURE)) {
            $this->orderMailSender->sendSellerMail($orderItem, $cart);

            return;
        }

        $this->mailHandler->setCart($cart);
        $this->mailHandler->sendSellerMail($orderItem);
    }
}
