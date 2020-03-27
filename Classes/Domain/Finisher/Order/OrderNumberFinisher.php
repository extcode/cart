<?php

namespace Extcode\Cart\Domain\Finisher\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class OrderNumberFinisher extends \Extcode\Cart\Domain\Finisher\AbstractFinisher
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
