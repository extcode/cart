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
use Extcode\Cart\Domain\Repository\Order\ShippingRepository;
use Extcode\Cart\Event\Order\PersistOrderEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class Shipping
{
    public function __construct(
        private readonly PersistenceManager $persistenceManager,
        private readonly ItemRepository $itemRepository,
        private readonly ShippingRepository $shippingRepository
    ) {}

    public function __invoke(PersistOrderEvent $event): void
    {
        $cart = $event->getCart();
        $orderItem = $event->getOrderItem();
        $storagePid = $event->getStoragePid();
        $taxClasses = $event->getTaxClasses();

        $shipping = $cart->getShipping();

        $orderShipping = GeneralUtility::makeInstance(
            \Extcode\Cart\Domain\Model\Order\Shipping::class
        );
        $orderShipping->setItem($orderItem);
        $orderShipping->setPid($storagePid);

        if ($cart->getShippingCountry()) {
            $orderShipping->setServiceCountry($cart->getShippingCountry());
        } elseif ($cart->getBillingCountry()) {
            $orderShipping->setServiceCountry($cart->getBillingCountry());
        }
        $orderShipping->setServiceId($shipping->getId());
        $orderShipping->setName($shipping->getName());
        $orderShipping->setStatus($shipping->getStatus());
        $orderShipping->setGross($shipping->getGross());
        $orderShipping->setNet($shipping->getNet());
        if ($shipping->getTaxClass()->getId() > 0) {
            $orderShipping->setTaxClass($taxClasses[$shipping->getTaxClass()->getId()]);
        }
        $orderShipping->setTax($shipping->getTax());
        if (method_exists($shipping, 'getNote')) {
            $orderShipping->setNote($shipping->getNote());
        }

        $this->shippingRepository->add($orderShipping);
        $orderItem->setShipping($orderShipping);
        $this->itemRepository->update($orderItem);
        $this->persistenceManager->persistAll();
    }
}
