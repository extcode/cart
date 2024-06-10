<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use Extcode\Cart\Domain\Model\Order\Tax as OrderTax;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Extcode\Cart\Domain\Repository\Order\TaxRepository;
use Extcode\Cart\Event\Order\PersistOrderEventInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class Taxes
{
    public function __construct(
        private readonly PersistenceManager $persistenceManager,
        private readonly ItemRepository $itemRepository,
        private readonly TaxRepository $taxRepository
    ) {}

    public function __invoke(PersistOrderEventInterface $event): void
    {
        $orderItem = $event->getOrderItem();

        $orderItem = $this->addTaxToRepositoryAndItem($event, $orderItem, 'tax');
        $orderItem = $this->addTaxToRepositoryAndItem($event, $orderItem, 'totalTax');

        $this->itemRepository->update($orderItem);
        $this->persistenceManager->persistAll();
    }

    protected function addTaxToRepositoryAndItem(
        PersistOrderEventInterface $event,
        OrderItem $orderItem,
        string $typeOfTax
    ): OrderItem {
        $cart = $event->getCart();
        $storagePid = $event->getStoragePid();
        $taxClasses = $event->getTaxClasses();

        $taxes = $typeOfTax === 'tax' ? $cart->getTaxes() : $cart->getTotalTaxes();

        foreach ($taxes as $taxClassId => $taxValue) {
            $orderTax = GeneralUtility::makeInstance(
                OrderTax::class,
                $taxValue,
                $taxClasses[$taxClassId]
            );
            $orderTax->setPid($storagePid);

            $this->taxRepository->add($orderTax);

            if ($typeOfTax === 'tax') {
                $orderItem->addTax($orderTax);
            } else {
                $orderItem->addTotalTax($orderTax);
            }
        }

        return $orderItem;
    }
}
