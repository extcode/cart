<?php

namespace Extcode\Cart\Controller\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Utility\OrderUtility;

class CartPreviewController extends ActionController
{
    /**
     * @var OrderUtility
     */
    private $orderUtility;

    public function injectOrderUtility(
        OrderUtility $orderUtility
    ) {
        $this->orderUtility = $orderUtility;
    }

    public function showAction()
    {
        $this->restoreSession();
        $this->view->assign('cart', $this->cart);
    }
}
