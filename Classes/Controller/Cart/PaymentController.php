<?php

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PaymentController extends ActionController
{
    const AJAX_CART_TYPE_NUM = '2278001';

    /**
     * Action update
     *
     * @param int $paymentId
     */
    public function updateAction($paymentId)
    {
        $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

        $this->payments = $this->parserUtility->parseServices('Payment', $this->pluginSettings, $this->cart);

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
                    \TYPO3\CMS\Core\Messaging\AbstractMessage::ERROR,
                    true
                );
            }
        }

        $this->sessionHandler->write($this->cart, $this->settings['cart']['pid']);

        $pageType = $GLOBALS['TYPO3_REQUEST']->getAttribute('routing')->getPageType();
        if ($pageType === self::AJAX_CART_TYPE_NUM) {
            $this->view->assign('cart', $this->cart);

            $this->parseData();
            $assignArguments = [
                'shippings' => $this->shippings,
                'payments' => $this->payments,
                'specials' => $this->specials
            ];
            $this->view->assignMultiple($assignArguments);
        } else {
            $this->redirect('show', 'Cart\Cart');
        }
    }
}
