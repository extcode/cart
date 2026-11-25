<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

final readonly class OrderProductQuantity
{
    public function __construct(
        public int $orderProductUid,
        public bool $isShippable,
        public int $orderedCount,
        public int $countInUncanceledShipments = 0,
        public int $shippedCount = 0,
    ) {
    }

    public function getCountAvailableForShipment(): int
    {
        if (!$this->isShippable) {
            return 0;
        }

        return max(0, $this->orderedCount - $this->countInUncanceledShipments);
    }

    public function isCompletelyShipped(): bool
    {
        return $this->shippedCount >= $this->orderedCount;
    }
}
