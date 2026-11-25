<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Status\StatusChangeResult;

final readonly class ShipmentStatusChangeResult
{
    public function __construct(
        public StatusChangeResult $shipmentStatusChange,
        public ?StatusChangeResult $shippingStatusChange = null,
    ) {
    }

    public function isChanged(): bool
    {
        return $this->shipmentStatusChange->isChanged();
    }
}
