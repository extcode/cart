<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Domain\Model\Cart\CartCouponFix;
use Extcode\Cart\Domain\Model\Cart\CartCouponInterface;
use Extcode\Cart\Domain\Model\Coupon;
use Extcode\Cart\Domain\Repository\CouponRepository;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class CouponController extends ActionController
{
    protected CouponRepository $couponRepository;

    public function injectCouponRepository(CouponRepository $couponRepository): void
    {
        $this->couponRepository = $couponRepository;
    }

    public function addAction(): ResponseInterface
    {
        if ($this->request->hasArgument('couponCode')) {
            $this->restoreSession();

            $couponCode = $this->request->getArgument('couponCode');

            $coupon = $this->couponRepository->findOneBy(['code' => $couponCode]);
            if (
                $coupon instanceof Coupon &&
                $coupon->isAvailable()
            ) {
                $couponType = $coupon->getCouponType();

                // will be removed in version 9.x for TYPO3 v12 and TYPO3 v11
                // TODO: provide an upgrade wizard to change the coupon_type in Database
                if ($couponType === 'cartdiscount') {
                    $couponType = CartCouponFix::class;
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
                        $coupon->isCombinable()
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

            $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);

            return $this->htmlResponse();
        }

        return $this->redirect('show', 'Cart\Cart');
    }

    public function removeAction(): ResponseInterface
    {
        if ($this->request->hasArgument('couponCode')) {
            $this->restoreSession();
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

            $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);

            return $this->htmlResponse();
        }

        return $this->redirect('show', 'Cart\Cart');
    }
}
