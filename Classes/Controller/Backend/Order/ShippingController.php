<?php

namespace Extcode\Cart\Controller\Backend\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class ShippingController extends \Extcode\Cart\Controller\Backend\ActionController
{

    /**
     * Order Shipping Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ShippingRepository
     */
    protected $shippingRepository;

    /**
     * @param \Extcode\Cart\Domain\Repository\Order\ShippingRepository $shippingRepository
     */
    public function injectShippingRepository(
        \Extcode\Cart\Domain\Repository\Order\ShippingRepository $shippingRepository
    ) {
        $this->shippingRepository = $shippingRepository;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Order\Shipping $shipping
     */
    public function updateAction(\Extcode\Cart\Domain\Model\Order\Shipping $shipping)
    {
        $this->shippingRepository->update($shipping);

        $msg = LocalizationUtility::translate(
            'tx_cart.controller.order.action.update_shipping_action.success',
            $this->extensionName
        );

        $this->addFlashMessage($msg);

        $this->redirect('show', 'Backend\Order\Order', null, ['orderItem' => $shipping->getItem()]);
    }
}
