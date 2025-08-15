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
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class CouponController extends ActionController
{
    public function __construct(
        protected CouponRepository $couponRepository
    ) {}

    public function addAction(): ResponseInterface
    {
        if ($this->request->hasArgument('couponCode')) {
            $this->restoreSession();

            $couponCode = $this->request->getArgument('couponCode');

            $coupon = $this->couponRepository->findOneBy(['code' => $couponCode]);
            if (
                $coupon instanceof Coupon
                && $coupon->isAvailable()
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

                    $this->addFlashMessageForAddedCoupon($couponWasAdded, $coupon);
                } else {
                    $this->addFlashMessage(
                        LocalizationUtility::translate(
                            'tx_cart.error.coupon.not_accepted',
                            'Cart'
                        ),
                        '',
                        ContextualFeedbackSeverity::WARNING,
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
                    ContextualFeedbackSeverity::WARNING,
                    true
                );
            }

            $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);
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
                    ContextualFeedbackSeverity::OK,
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
                    ContextualFeedbackSeverity::WARNING,
                    true
                );
            }

            $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);
        }

        return $this->redirect('show', 'Cart\Cart');
    }

    private function addFlashMessageForAddedCoupon(int $couponWasAdded, Coupon $coupon): void
    {
        if ($couponWasAdded === 1) {
            $messageBody = LocalizationUtility::translate(
                'tx_cart.ok.coupon.added',
                'Cart'
            );

            foreach ($this->cart->getCoupons() as $cartCoupon) {
                if ($cartCoupon->getCode() !== $coupon->getCode()) {
                    continue;
                }

                if ($cartCoupon->isUseable()) {
                    $this->addFlashMessage(
                        $messageBody
                    );
                } else {
                    $this->addFlashMessage(
                        LocalizationUtility::translate(
                            'tx_cart.error.coupon.added_but_not_usable',
                            'Cart'
                        ),
                        '',
                        ContextualFeedbackSeverity::WARNING,
                    );
                }
            }

            return;
        }

        if ($couponWasAdded === -1) {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'tx_cart.error.coupon.already_added',
                    'Cart'
                ),
                '',
                ContextualFeedbackSeverity::WARNING,
            );

            return;
        }

        if ($couponWasAdded === -2) {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'tx_cart.error.coupon.not_combinable',
                    'Cart'
                ),
                '',
                ContextualFeedbackSeverity::WARNING,
            );
        }
    }
}
