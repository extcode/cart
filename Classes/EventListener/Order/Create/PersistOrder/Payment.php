<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Extcode\Cart\Domain\Repository\Order\PaymentRepository;
use Extcode\Cart\Event\Order\PersistOrderEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class Payment
{
    public function __construct(
        private readonly PersistenceManager $persistenceManager,
        private readonly ItemRepository $itemRepository,
        private readonly PaymentRepository $paymentRepository
    ) {}

    public function __invoke(PersistOrderEvent $event): void
    {
        $cart = $event->getCart();
        $orderItem = $event->getOrderItem();
        $storagePid = $event->getStoragePid();
        $taxClasses = $event->getTaxClasses();

        $payment = $cart->getPayment();

        $orderPayment = GeneralUtility::makeInstance(
            \Extcode\Cart\Domain\Model\Order\Payment::class
        );
        $orderPayment->setItem($orderItem);
        $orderPayment->setPid($storagePid);

        if ($cart->getBillingCountry()) {
            $orderPayment->setServiceCountry($cart->getBillingCountry());
        }
        $orderPayment->setServiceId($payment->getId());
        $orderPayment->setName($payment->getName());
        if (method_exists($payment, 'getProvider')) {
            $orderPayment->setProvider($payment->getProvider());
        }
        $orderPayment->setStatus($payment->getStatus());
        $orderPayment->setGross($payment->getGross());
        $orderPayment->setNet($payment->getNet());
        if ($payment->getTaxClass()->getId() > 0) {
            $orderPayment->setTaxClass($taxClasses[$payment->getTaxClass()->getId()]);
        }
        $orderPayment->setTax($payment->getTax());
        if (method_exists($payment, 'getNote')) {
            $orderPayment->setNote($payment->getNote());
        }

        $this->paymentRepository->add($orderPayment);
        $orderItem->setPayment($orderPayment);
        $this->itemRepository->update($orderItem);
        $this->persistenceManager->persistAll();
    }
}
