<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Controller\Backend\ActionController;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Domain\Repository\Order\PaymentRepository;
use Extcode\Cart\Event\Order\UpdateServiceEvent;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PaymentController extends ActionController
{
    protected PaymentRepository $paymentRepository;

    public function __construct(
        PaymentRepository $paymentRepository
    ) {
        $this->paymentRepository = $paymentRepository;
    }

    public function updateAction(Payment $payment): ResponseInterface
    {
        $this->paymentRepository->update($payment);

        $event = new UpdateServiceEvent($payment);
        $this->eventDispatcher->dispatch($event);

        $msg = LocalizationUtility::translate(
            'tx_cart.controller.order.action.update_payment_action.success',
            'Cart'
        );

        $this->addFlashMessage($msg);

        return $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $payment->getItem()]);
    }
}
