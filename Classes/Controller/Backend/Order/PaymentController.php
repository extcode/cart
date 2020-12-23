<?php

namespace Extcode\Cart\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Domain\Repository\Order\PaymentRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PaymentController extends \Extcode\Cart\Controller\Backend\ActionController
{
    /**
     * @var PaymentRepository
     */
    protected $paymentRepository;

    /**
     * @param PaymentRepository $paymentRepository
     */
    public function injectPaymentRepository(PaymentRepository $paymentRepository)
    {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param Payment $payment
     */
    public function updateAction(Payment $payment)
    {
        $this->paymentRepository->update($payment);

        $msg = LocalizationUtility::translate(
            'tx_cart.controller.order.action.update_payment_action.success',
            'Cart'
        );

        $this->addFlashMessage($msg);

        $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $payment->getItem()]);
    }
}
