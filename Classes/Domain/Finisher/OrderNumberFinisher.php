<?php

namespace Extcode\Cart\Domain\Finisher;

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
 * Order Number Finisher
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class OrderNumberFinisher extends AbstractFinisher
{
    /**
     * Item Repository
     *
     * @var \Extcode\Cart\Domain\Repository\Order\ItemRepository
     */
    protected $orderItemRepository;

    /**
     * Order Utility
     *
     * @var \Extcode\Cart\Utility\OrderUtility
     */
    protected $orderUtility;

    /**
     * @param \Extcode\Cart\Domain\Repository\Order\ItemRepository $orderItemRepository
     */
    public function injectOrderItemRepository(
        \Extcode\Cart\Domain\Repository\Order\ItemRepository $orderItemRepository
    ) {
        $this->orderItemRepository = $orderItemRepository;
    }

    /**
     * @param \Extcode\Cart\Utility\OrderUtility $orderUtility
     */
    public function injectOrderUtility(
        \Extcode\Cart\Utility\OrderUtility $orderUtility
    ) {
        $this->orderUtility = $orderUtility;
    }

    public function executeInternal()
    {
        $cart = $this->finisherContext->getCart();
        $orderItem = $this->finisherContext->getOrderItem();

        $orderNumber = $this->orderUtility->getNumber($this->settings, 'order');

        $orderItem->setOrderNumber($orderNumber);
        $orderItem->setOrderDate(new \DateTime());

        $this->orderItemRepository->update($orderItem);

        $this->persistenceManager->persistAll();

        $this->finisherContext->setOrderItem($orderItem);

        $cart->setOrderId($orderItem->getUid());
        $cart->setOrderNumber($orderItem->getOrderNumber());
    }
}
