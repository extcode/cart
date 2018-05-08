<?php

namespace Extcode\Cart\Controller\Backend\Order;

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
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

/**
 * Shipping Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
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
