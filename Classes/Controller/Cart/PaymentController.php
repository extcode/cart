<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Psr\Http\Message\ResponseInterface;
use TYPO3\CMS\Core\Type\ContextualFeedbackSeverity;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PaymentController extends ActionController
{
    public const AJAX_CART_TYPE_NUM = '2278001';

    public function updateAction(int $paymentId): ResponseInterface
    {
        $this->updatePaymentInSession($paymentId);

        if ($this->isAjaxRequest()) {
            return $this->renderHtmlResponse();
        }

        return $this->redirect('show', 'Cart\Cart');
    }

    private function isAjaxRequest(): bool
    {
        $pageType = $GLOBALS['TYPO3_REQUEST']->getAttribute('routing')->getPageType();

        return $pageType === self::AJAX_CART_TYPE_NUM;
    }

    private function renderHtmlResponse(): ResponseInterface
    {
        $this->view->assign('cart', $this->cart);

        $this->parseServicesAndAssignToView();
        $this->dispatchModifyViewEvent();

        return $this->htmlResponse();
    }

    private function updatePaymentInSession(int $paymentId): void
    {
        $this->restoreSession();

        $payments = $this->paymentMethodsService->getPaymentMethods($this->cart);
        $payment = $payments[$paymentId] ?? null;

        if (is_null($payment) || $payment->isAvailable() === false) {
            $this->addFlashMessage(
                LocalizationUtility::translate(
                    'tx_cart.controller.cart.action.set_payment.not_available',
                    'Cart'
                ),
                '',
                ContextualFeedbackSeverity::ERROR,
                true
            );

            return;
        }

        $this->cart->setPayment($payment);
        $this->sessionHandler->writeCart($this->settings['cart']['pid'], $this->cart);
    }
}
