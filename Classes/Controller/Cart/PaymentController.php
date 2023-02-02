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
use TYPO3\CMS\Core\Messaging\AbstractMessage;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PaymentController extends ActionController
{
    public const AJAX_CART_TYPE_NUM = '2278001';

    public function updateAction(int $paymentId): ResponseInterface
    {
        $this->restoreSession();

        $this->payments = $this->parserUtility->parseServices('Payment', $this->configurations, $this->cart);

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
                    AbstractMessage::ERROR,
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
