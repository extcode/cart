<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Repository\FrontendUserRepository;
use Extcode\Cart\Domain\Repository\Order\BillingAddressRepository;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Extcode\Cart\Domain\Repository\Order\ShippingAddressRepository;
use Extcode\Cart\Event\Order\PersistOrderEventInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class Item
{
    private PersistenceManager $persistenceManager;

    private ItemRepository $itemRepository;

    private BillingAddressRepository $billingAddressRepository;

    private ShippingAddressRepository $shippingAddressRepository;

    public function __construct(
        PersistenceManager $persistenceManager,
        ItemRepository $itemRepository,
        BillingAddressRepository $billingAddressRepository,
        ShippingAddressRepository $shippingAddressRepository
    ) {
        $this->persistenceManager = $persistenceManager;
        $this->itemRepository = $itemRepository;
        $this->billingAddressRepository = $billingAddressRepository;
        $this->shippingAddressRepository = $shippingAddressRepository;
    }

    public function __invoke(PersistOrderEventInterface $event): void
    {
        $settings = $event->getSettings();
        $storagePid = $event->getStoragePid();
        $cart = $event->getCart();
        $orderItem = $event->getOrderItem();

        $orderItem->setPid($storagePid);

        if(isset($GLOBALS['TSFE']->fe_user->user['uid'])) {
            $feUserId = (int)$GLOBALS['TSFE']->fe_user->user['uid'];
            if ($feUserId) {
                $frontendUserRepository = GeneralUtility::makeInstance(
                    FrontendUserRepository::class
                );
                $feUser = $frontendUserRepository->findByUid($feUserId);
                if ($feUser) {
                    $orderItem->setFeUser($feUser);
                }
            }
        }

        $orderItem->setCurrency($settings['settings']['format']['currency']['currencySign']);
        $orderItem->setCurrencyCode($cart->getCurrencyCode());
        $orderItem->setCurrencySign($cart->getCurrencySign());
        $orderItem->setCurrencyTranslation($cart->getCurrencyTranslation());
        $orderItem->setGross($cart->getGross());
        $orderItem->setNet($cart->getNet());
        $orderItem->setTotalGross($cart->getTotalGross());
        $orderItem->setTotalNet($cart->getTotalNet());

        $this->itemRepository->add($orderItem);

        if ($orderItem->getBillingAddress()) {
            $billingAddress = $orderItem->getBillingAddress();
            $billingAddress->setPid($storagePid);
            $billingAddress->setItem($orderItem);
            $this->billingAddressRepository->add($billingAddress);
        }
        if ($orderItem->getShippingAddress()) {
            $shippingAddress = $orderItem->getShippingAddress();
            $shippingAddress->setPid($storagePid);
            $shippingAddress->setItem($orderItem);
            $this->shippingAddressRepository->add($shippingAddress);
        }

        $this->persistenceManager->persistAll();
    }
}
