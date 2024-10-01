<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Coupon;
use Extcode\Cart\Domain\Model\Order\Discount;
use Extcode\Cart\Domain\Repository\CouponRepository;
use Extcode\Cart\Domain\Repository\Order\DiscountRepository;
use Extcode\Cart\Domain\Repository\Order\ItemRepository;
use Extcode\Cart\Event\Order\PersistOrderEvent;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class Coupons
{
    public function __construct(
        private readonly PersistenceManager $persistenceManager,
        private readonly ItemRepository $itemRepository,
        private readonly DiscountRepository $discountRepository,
        private readonly CouponRepository $couponRepository
    ) {}

    public function __invoke(PersistOrderEvent $event): void
    {
        $cart = $event->getCart();
        $orderItem = $event->getOrderItem();
        $storagePid = $event->getStoragePid();

        if (!$cart->getCoupons()) {
            return;
        }

        foreach ($cart->getCoupons() as $cartCoupon) {
            if ($cartCoupon->isUseable()) {
                $orderDiscount = GeneralUtility::makeInstance(
                    Discount::class,
                    $cartCoupon->getTitle(),
                    $cartCoupon->getCode(),
                    $cartCoupon->getGross(),
                    $cartCoupon->getNet(),
                    $cartCoupon->getTaxClass(),
                    $cartCoupon->getTax()
                );
                $orderDiscount->setPid($storagePid);

                $this->discountRepository->add($orderDiscount);

                $orderItem->addDiscount($orderDiscount);

                $coupon = $this->couponRepository->findOneBy(['code' => $cartCoupon->getCode()]);
                if ($coupon instanceof Coupon) {
                    $coupon->incNumberUsed();
                    $this->couponRepository->update($coupon);
                }
            }
        }

        $this->itemRepository->update($orderItem);

        $this->persistenceManager->persistAll();
    }
}
