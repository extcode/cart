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
use Extcode\Cart\Event\Order\PersistOrderEvent;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class Item
{
    public function __construct(
        private readonly PersistenceManager $persistenceManager,
        private readonly ItemRepository $itemRepository,
        private readonly BillingAddressRepository $billingAddressRepository,
        private readonly ShippingAddressRepository $shippingAddressRepository
    ) {}

    public function __invoke(PersistOrderEvent $event): void
    {
        $settings = $event->getSettings();
        $storagePid = $event->getStoragePid();
        $cart = $event->getCart();
        $orderItem = $event->getOrderItem();

        $orderItem->setPid($storagePid);

        /** @var  $userAspect */
        $userAspect = GeneralUtility::makeInstance(Context::class)->getAspect('frontend.user');

        if ($userAspect->isLoggedIn()) {
            $frontendUserRepository = GeneralUtility::makeInstance(
                FrontendUserRepository::class
            );
            $feUser = $frontendUserRepository->findByUid($userAspect->get('id'));
            if ($feUser) {
                $orderItem->setFeUser($feUser);
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

        /* In multistep checkout the setting `shippingSameAsBilling` might get lost for the orderItem,
           but it does not get lost for the cart as the cart is stored between every step in the session */
        $orderItem->setShippingSameAsBilling($cart->isShippingSameAsBilling());
        if ($orderItem->isShippingSameAsBilling()) {
            $orderItem->removeShippingAddress();
        }

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
