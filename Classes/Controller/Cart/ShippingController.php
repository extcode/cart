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

class ShippingController extends ActionController
{
    public const AJAX_CART_TYPE_NUM = '2278001';

    public function updateAction(int $shippingId): ResponseInterface
    {
        $this->restoreSession();

        $this->shippings = $this->parserUtility->parseServices('Shipping', $this->configurations, $this->cart);

        $shipping = $this->shippings[$shippingId];

        if ($shipping) {
            if ($shipping->isAvailable($this->cart->getGross())) {
                $this->cart->setShipping($shipping);
            } else {
                $this->addFlashMessage(
                    LocalizationUtility::translate(
                        'tx_cart.controller.cart.action.set_shipping.not_available',
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
