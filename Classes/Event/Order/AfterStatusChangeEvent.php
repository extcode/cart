<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\AbstractService;
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\OrderHistory\OrderChange;
use Extcode\Cart\OrderStatus\ServiceType;

/**
 * Dispatched by the StatusChangeService after the payment or the shipping
 * status of an order was changed and written to the order history, e.g. to
 * send an email or to create a shipping label.
 *
 * The uid of the order change (getOrderChange()->uid) identifies this change,
 * a listener can use it to execute an action only once.
 */
final readonly class AfterStatusChangeEvent
{
    public function __construct(
        private Item $orderItem,
        private AbstractService $service,
        private ServiceType $serviceType,
        private OrderChange $orderChange,
    ) {
    }

    public function getOrderItem(): Item
    {
        return $this->orderItem;
    }

    /**
     * The payment or the shipping, with the new status already.
     */
    public function getService(): AbstractService
    {
        return $this->service;
    }

    public function getServiceType(): ServiceType
    {
        return $this->serviceType;
    }

    /**
     * The stored change with the old and the new status, the source, the initiator and the backend user.
     */
    public function getOrderChange(): OrderChange
    {
        return $this->orderChange;
    }

    public function getFromStatus(): string
    {
        return $this->orderChange->oldValue;
    }

    public function getToStatus(): string
    {
        return $this->orderChange->newValue;
    }
}
