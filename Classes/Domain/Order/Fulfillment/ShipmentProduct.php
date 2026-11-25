<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

final readonly class ShipmentProduct
{
    public function __construct(
        public int $orderProductUid,
        public int $count,
        public int $uid = 0,
    ) {
    }
}
