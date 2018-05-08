<?php

namespace Extcode\Cart\Domain\Finisher;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Payment Finisher
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class PaymentFinisher extends AbstractFinisher
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
