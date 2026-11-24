<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\CartCouponInterface;
use Extcode\Cart\Domain\Model\Coupon;
use Extcode\Cart\Domain\Model\Order\Discount;
use Extcode\Cart\Domain\Model\Order\TaxClass;
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
    ) {
    }

    public function __invoke(PersistOrderEvent $event): void
    {
        $cart = $event->getCart();
        $orderItem = $event->getOrderItem();
        $storagePid = $event->getStoragePid();
        $taxClasses = array_filter(
            $event->getTaxClasses(),
            static fn (mixed $taxClass): bool => $taxClass instanceof TaxClass
        );

        if (!$cart->getCoupons()) {
            return;
        }

        foreach ($cart->getCoupons() as $cartCoupon) {
            if ($cartCoupon instanceof CartCouponInterface && $cartCoupon->isUseable()) {
                $orderDiscount = GeneralUtility::makeInstance(
                    Discount::class,
                    $cartCoupon->getTitle(),
                    $cartCoupon->getCode(),
                    $cartCoupon->getGross(),
                    $cartCoupon->getNet(),
                    $taxClasses[$cartCoupon->getTaxClass()->getId()] ?? null,
                    $cartCoupon->getTax(),
                    $cartCoupon::class,
                    self::getTaxesPerOrderTaxClass($cartCoupon, $taxClasses)
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

    /**
     * Returns the taxes of the coupon per tax class of the order. They are
     * calculated like in Cart::getCouponTaxes(), so the taxes of all discounts
     * of an order add up to the coupon taxes of the cart.
     *
     * @param array<TaxClass> $taxClasses id of the tax class of the cart => tax class of the order
     *
     * @return array<int, float> uid of the tax class of the order => tax
     */
    private static function getTaxesPerOrderTaxClass(CartCouponInterface $cartCoupon, array $taxClasses): array
    {
        $cartTaxes = [
            $cartCoupon->getTaxClass()->getId() => $cartCoupon->getTax(),
        ];
        $couponTaxes = method_exists($cartCoupon, 'getTaxes') ? $cartCoupon->getTaxes() : [];
        foreach (is_array($couponTaxes) ? $couponTaxes : [] as $taxClassId => $tax) {
            if (is_int($taxClassId) && is_numeric($tax)) {
                $cartTaxes[$taxClassId] = ($cartTaxes[$taxClassId] ?? 0.0) + (float) $tax;
            }
        }

        $taxes = [];
        foreach ($cartTaxes as $taxClassId => $tax) {
            $taxClassUid = ($taxClasses[$taxClassId] ?? null)?->getUid();
            if ($taxClassUid !== null) {
                // rounded like the amounts in the decimal(17,5) columns
                $taxes[$taxClassUid] = round($tax, 5);
            }
        }

        return $taxes;
    }
}
