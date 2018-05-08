<?php

namespace Extcode\Cart\Controller\Backend\Order;

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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Payment Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class PaymentController extends \Extcode\Cart\Controller\Backend\ActionController
{
    /**
     * Order Payment Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\PaymentRepository
     */
    protected $paymentRepository;

    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;
    /**
     * @param \Extcode\Cart\Domain\Repository\Order\PaymentRepository $paymentRepository
     */
    public function injectPaymentRepository(
        \Extcode\Cart\Domain\Repository\Order\PaymentRepository $paymentRepository
    ) {
        $this->paymentRepository = $paymentRepository;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Order\Payment $payment
     */
    public function updateAction(\Extcode\Cart\Domain\Model\Order\Payment $payment)
    {
        $this->paymentRepository->update($payment);

        $msg = LocalizationUtility::translate(
            'tx_cart.controller.order.action.update_payment_action.success',
            $this->extensionName
        );

        $this->addFlashMessage($msg);

        $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $payment->getItem()]);
    }
}
