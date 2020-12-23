<?php

namespace Extcode\Cart\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Shipping;
use Extcode\Cart\Domain\Repository\Order\ShippingRepository;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ShippingController extends \Extcode\Cart\Controller\Backend\ActionController
{
    /**
     * @var ShippingRepository
     */
    protected $shippingRepository;

    /**
     * @param ShippingRepository $shippingRepository
     */
    public function injectShippingRepository(ShippingRepository $shippingRepository)
    {
        $this->shippingRepository = $shippingRepository;
    }

    /**
     * @param Shipping $shipping
     */
    public function updateAction(Shipping $shipping)
    {
        $this->shippingRepository->update($shipping);

        $msg = LocalizationUtility::translate(
            'tx_cart.controller.order.action.update_shipping_action.success',
            'Cart'
        );

        $this->addFlashMessage($msg);

        $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $shipping->getItem()]);
    }
}
