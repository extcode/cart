<?php

namespace Extcode\Cart\Controller\Cart;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Cart Shipping Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ShippingController extends ActionController
{
    const AJAX_CART_TYPE_NUM = '2278001';

    /**
     * Action update
     *
     * @param int $shippingId
     */
    public function updateAction($shippingId)
    {
        $this->cart = $this->sessionHandler->restore($this->settings['cart']['pid']);

        $this->shippings = $this->parserUtility->parseServices('Shipping', $this->pluginSettings, $this->cart);

        $shipping = $this->shippings[$shippingId];

        if ($shipping) {
            if ($shipping->isAvailable($this->cart->getGross())) {
                $this->cart->setShipping($shipping);
            } else {
                $this->addFlashMessage(
                    \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                        'tx_cart.controller.cart.action.set_shipping.not_available',
                        $this->extensionName
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
