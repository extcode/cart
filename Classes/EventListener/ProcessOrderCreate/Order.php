<?php
declare(strict_types=1);
namespace Extcode\Cart\EventListener\ProcessOrderCreate;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Order\EventInterface;
use Extcode\Cart\Utility\OrderUtility;

class Order
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

    public function __invoke(EventInterface $event): void
    {
        $this->orderUtility->saveOrderItem(
            $event->getSettings(),
            $event->getCart(),
            $event->getOrderItem()
        );
    }
}
