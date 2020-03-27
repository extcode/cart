<?php

namespace Extcode\Cart\Domain\Finisher\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class PaymentFinisher extends \Extcode\Cart\Domain\Finisher\AbstractFinisher
{
    /**
     * Payment Utility
     *
     * @var \Extcode\Cart\Utility\PaymentUtility
     */
    protected $paymentUtility;

    /**
     * @param \Extcode\Cart\Utility\PaymentUtility $paymentUtility
     */
    public function injectPaymentUtility(
        \Extcode\Cart\Utility\PaymentUtility $paymentUtility
    ) {
        $this->paymentUtility = $paymentUtility;
    }

    public function executeInternal()
    {
        $cart = $this->finisherContext->getCart();
        $orderItem = $this->finisherContext->getOrderItem();

        $providerUsed = $this->paymentUtility->handlePayment($orderItem, $cart);

        if ($providerUsed) {
            $this->finisherContext->cancel();
        }
    }
}
