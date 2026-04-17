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
use Extcode\Cart\Service\MailHandler;

class Email
{
    public function __construct(
        private readonly PaymentMethodsLoaderInterface $paymentMethodsLoader,
        private readonly MailHandler $mailHandler,
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
        $this->mailHandler->setCart($cart);
        $this->mailHandler->sendBuyerMail($orderItem);
    }

    protected function sendSellerMail(Item $orderItem, Cart $cart): void
    {
        $this->mailHandler->setCart($cart);
        $this->mailHandler->sendSellerMail($orderItem);
    }
}
