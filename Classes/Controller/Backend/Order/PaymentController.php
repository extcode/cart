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
use Extcode\Cart\Domain\Log\LogServiceInterface;
use Extcode\Cart\Domain\Log\Model\Log;
use Extcode\Cart\Domain\Model\Order\Payment;
use Extcode\Cart\Domain\Repository\Order\PaymentRepository;
use Extcode\Cart\Event\Order\UpdateServiceEvent;
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PaymentController extends ActionController
{
    public function __construct(
        private readonly PaymentRepository $paymentRepository,
        private readonly LogServiceInterface $logService,
    ) {}

    public function updateAction(Payment $payment): ResponseInterface
    {
        $this->paymentRepository->update($payment);
        $this->logService->write(
            Log::info(
                $this->getOrderItemUid($payment),
                'updatePayment',
                'Payment was set to ' . $payment->getStatus() . '.',
                [
                    'time' => time(),
                ]
            )
        );

        $event = new UpdateServiceEvent($payment);
        $this->eventDispatcher->dispatch($event);

        $msg = LocalizationUtility::translate(
            'tx_cart.controller.order.action.update_payment_action.success',
            'Cart'
        );

        $this->addFlashMessage($msg);

        return $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $payment->getItem()]);
    }

    private function getOrderItemUid(Payment $payment): int
    {
        $orderItemUid = $payment->getItem()?->getUid();

        if (is_null($orderItemUid)) {
            throw new InvalidArgumentException('Method should only called for persisted orders.', 1774715307);
        }

        return $orderItemUid;
    }
}
