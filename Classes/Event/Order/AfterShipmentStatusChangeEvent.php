<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Fulfillment\Shipment;
use Extcode\Cart\Domain\Order\History\OrderChange;

/**
 * Dispatched outside of the database transaction, so listeners may take time,
 * e.g. to send an email. The shipping status of the order is derived after
 * this event.
 */
final class AfterShipmentStatusChangeEvent
{
    /**
     * @var list<string>
     */
    private array $errors = [];

    public function __construct(
        private readonly Shipment $shipment,
        private readonly OrderChange $orderChange,
    ) {
    }

    public function addError(string $message): void
    {
        $this->errors[] = $message;
    }

    /**
     * @return list<string>
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function getShipmentWithNewStatus(): Shipment
    {
        return $this->shipment;
    }

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
