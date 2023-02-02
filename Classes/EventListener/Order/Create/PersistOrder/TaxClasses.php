<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\TaxClass;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Extcode\Cart\Domain\Repository\Order\TaxClassRepository;
use Extcode\Cart\Event\Order\PersistOrderEventInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class TaxClasses
{
    private PersistenceManager $persistenceManager;

    private ItemRepository $itemRepository;

    private TaxClassRepository $taxClassRepository;

    public function __construct(
        PersistenceManager $persistenceManager,
        ItemRepository $itemRepository,
        TaxClassRepository $taxClassRepository
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->itemRepository = $itemRepository;
        $this->taxClassRepository = $taxClassRepository;
    }

    public function __invoke(PersistOrderEventInterface $event): void
    {
        $cart = $event->getCart();
        $orderItem = $event->getOrderItem();
        $storagePid = $event->getStoragePid();
        $taxClasses = $event->getTaxClasses();

        foreach ($cart->getTaxClasses() as $taxClass) {
            $orderTaxClass = GeneralUtility::makeInstance(TaxClass::class);
            $orderTaxClass->setTitle($taxClass->getTitle());
            $orderTaxClass->setValue($taxClass->getValue());
            $orderTaxClass->setCalc($taxClass->getCalc());
            $orderTaxClass->setPid($storagePid);

            $this->taxClassRepository->add($orderTaxClass);

            $orderItem->addTaxClass($orderTaxClass);

            $taxClasses[$taxClass->getId()] = $orderTaxClass;
        }

        $this->itemRepository->update($orderItem);

        $this->persistenceManager->persistAll();

        $event->setTaxClasses($taxClasses);
    }
}
