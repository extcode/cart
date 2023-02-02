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

class CartPreviewController extends ActionController
{
    public function showAction(): ResponseInterface
    {
        $this->restoreSession();
        $this->view->assign('cart', $this->cart);

        return $this->htmlResponse();
    }
}
