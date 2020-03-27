<?php

namespace Extcode\Cart\Domain\Finisher\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class OrderFinisher extends \Extcode\Cart\Domain\Finisher\AbstractFinisher
{
    /**
     * Order Utility
     *
     * @var \Extcode\Cart\Utility\OrderUtility
     */
    protected $orderUtility;

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

        $this->orderUtility->saveOrderItem(
            $this->settings,
            $cart,
            $orderItem
        );
    }
}
