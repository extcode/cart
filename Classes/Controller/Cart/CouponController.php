<?php

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class CouponController extends ActionController
{
    /**
     * @var \Extcode\Cart\Domain\Repository\CouponRepository
     */
    protected $couponRepository;

    /**
     * @param \Extcode\Cart\Domain\Repository\CouponRepository $couponRepository
     */
    public function injectCouponRepository(
        \Extcode\Cart\Domain\Repository\CouponRepository $couponRepository
    ) {
        $this->couponRepository = $couponRepository;
    }

    /**
     * Action Add
     */
    public function addAction()
    {
        if ($this->request->hasArgument('couponCode')) {
            $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

            $couponCode = $this->request->getArgument('couponCode');

            /** @var \Extcode\Cart\Domain\Model\Coupon $coupon */
            $coupon = $this->couponRepository->findOneByCode($couponCode);
            if ($coupon && $coupon->getIsAvailable()) {
                $newCartCoupon = GeneralUtility::makeInstance(
                    \Extcode\Cart\Domain\Model\Cart\CartCoupon::class,
                    $coupon->getTitle(),
                    $coupon->getCode(),
                    $coupon->getCouponType(),
                    $coupon->getDiscount(),
                    $this->cart->getTaxClass($coupon->getTaxClassId()),
                    $coupon->getCartMinPrice(),
                    $coupon->getIsCombinable()
                );

                $couponWasAdded = $this->cart->addCoupon($newCartCoupon);

                if ($couponWasAdded == 1) {
                    $this->addFlashMessage(
                        LocalizationUtility::translate(
                            'tx_cart.ok.coupon.added',
                            'Cart'
                        ),
                        '',
                        \TYPO3\CMS\Core\Messaging\AbstractMessage::OK,
                        true
                    );
                }
                if ($couponWasAdded == -1) {
                    $this->addFlashMessage(
                        LocalizationUtility::translate(
                            'tx_cart.error.coupon.already_added',
                            'Cart'
                        ),
                        '',
                        \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                        true
                    );
                }
                if ($couponWasAdded == -2) {
                    $this->addFlashMessage(
                        LocalizationUtility::translate(
                            'tx_cart.error.coupon.not_combinable',
                            'Cart'
                        ),
                        '',
                        \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                        true
                    );
                }
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'tx_cart.error.coupon.not_accepted',
                        'Cart'
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                    true
                );
            }

            $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);
        }

        $this->redirect('show', 'Cart\Cart');
    }

    /**
     * Action Remove
     */
    public function removeAction()
    {
        if ($this->request->hasArgument('couponCode')) {
            $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);
            $couponCode = $this->request->getArgument('couponCode');
            $couponWasRemoved = $this->cart->removeCoupon($couponCode);

            if ($couponWasRemoved == 1) {
                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'tx_cart.ok.coupon.removed',
                        'Cart'
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::OK,
                    true
                );
            }
            if ($couponWasRemoved == -1) {
                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'tx_cart.error.coupon.not_found',
                        'Cart'
                    ),
                    '',
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::WARNING,
                    true
                );
            }

            $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);
        }

        $this->redirect('show', 'Cart\Cart');
    }
}
