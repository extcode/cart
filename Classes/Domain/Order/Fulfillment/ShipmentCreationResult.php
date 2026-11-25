<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\History\OrderChange;
use Extcode\Cart\Domain\Order\Status\StatusChangeResult;

final readonly class ShipmentCreationResult
{
    public function __construct(
        public ShipmentCreationOutcome $outcome,
        public ?Shipment $shipment = null,
        public ?OrderChange $orderChange = null,
        /**
         * @var list<int>
         */
        public array $exceededOrderProductUids = [],
        public ?StatusChangeResult $shippingStatusChange = null,
    ) {
    }

    public function isCreated(): bool
    {
        return $this->outcome === ShipmentCreationOutcome::Created;
    }
}
