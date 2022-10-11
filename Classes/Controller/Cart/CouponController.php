<?php

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Domain\Model\Cart\CartCoupon;
use Extcode\Cart\Domain\Model\Cart\CartCouponInterface;
use Extcode\Cart\Domain\Model\Coupon;
use Extcode\Cart\Domain\Repository\CouponRepository;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class CouponController extends ActionController
{
    /**
     * @var CouponRepository
     */
    protected $couponRepository;

    /**
     * @param CouponRepository $couponRepository
     */
    public function injectCouponRepository(
        CouponRepository $couponRepository
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

            /** @var Coupon $coupon */
            $coupon = $this->couponRepository->findOneByCode($couponCode);
            if ($coupon && $coupon->getIsAvailable()) {
                $couponType = $coupon->getCouponType();

                // will be removed in version 9.x for TYPO3 v12 and TYPO3 v11
                // TODO: provide an upgrade wizard to change the coupon_type in Database
                if ($couponType === 'cartdiscount') {
                    $couponType = CartCoupon::class;
                }

                $interfaces = class_implements($couponType);

                if (isset($interfaces[CartCouponInterface::class])) {
                    $newCartCoupon = GeneralUtility::makeInstance(
                        $couponType,
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
                            AbstractMessage::OK,
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
                            AbstractMessage::WARNING,
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
                            AbstractMessage::WARNING,
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
                        AbstractMessage::WARNING,
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
                    AbstractMessage::WARNING,
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
                    AbstractMessage::OK,
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
                    AbstractMessage::WARNING,
                    true
                );
            }

            $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);
        }

        $this->redirect('show', 'Cart\Cart');
    }
}
