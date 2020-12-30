<?php

namespace Extcode\Cart\Domain\Finisher\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\ProcessOrderCreateEvent;
use Extcode\Cart\Utility\OrderUtility;

class OrderFinisher
{
    /**
     * @var OrderUtility
     */
    protected $orderUtility;

    /**
     * @param OrderUtility $orderUtility
     */
    public function __construct(OrderUtility $orderUtility)
    {
        $this->orderUtility = $orderUtility;
    }

    public function __invoke(ProcessOrderCreateEvent $event): void
    {
        $this->orderUtility->saveOrderItem(
            $event->getSettings(),
            $event->getCart(),
            $event->getOrderItem()
        );
    }
}
