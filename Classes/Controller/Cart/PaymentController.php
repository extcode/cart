<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Service\PaymentMethodsServiceInterface;
use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PaymentController extends ActionController
{
    public const AJAX_CART_TYPE_NUM = '2278001';

    public function __construct(
        private readonly PaymentMethodsServiceInterface $paymentMethodsService
    ) {}

    public function updateAction(int $paymentId): ResponseInterface
    {
        $this->restoreSession();

        $this->payments = $this->paymentMethodsService->getPaymentMethods($this->cart);

        $payment = $this->payments[$paymentId];

        if ($payment) {
            if ($payment->isAvailable($this->cart->getGross())) {
                $this->cart->setPayment($payment);
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'tx_cart.controller.cart.action.set_payment.not_available',
                        'Cart'
                    ),
                    '',
                    ContextualFeedbackSeverity::ERROR,
                    true
                );
            }
        }

        $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);

        $pageType = $GLOBALS['TYPO3_REQUEST']->getAttribute('routing')->getPageType();
        if ($pageType === self::AJAX_CART_TYPE_NUM) {
            $this->view->assign('cart', $this->cart);

            $this->parseServicesAndAssignToView();
            return $this->htmlResponse();
        }

        return $this->redirect('show', 'Cart\Cart');
    }
}
