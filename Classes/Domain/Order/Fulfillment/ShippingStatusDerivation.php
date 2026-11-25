<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Status\ShippingStatus;

final class ShippingStatusDerivation
{
    private const STATUSES_DERIVED_FROM_SHIPMENTS = [
        ShippingStatus::PARTIALLY_SHIPPED,
        ShippingStatus::SHIPPED,
    ];

    public static function derive(string $storedStatus, OrderProductQuantities $orderProductQuantities): string
    {
        return match (true) {
            !$orderProductQuantities->hasShippableProducts() => ShippingStatus::NOT_REQUIRED,
            $orderProductQuantities->isEverythingShipped() => ShippingStatus::SHIPPED,
            $orderProductQuantities->isAnythingShipped() => ShippingStatus::PARTIALLY_SHIPPED,
            in_array($storedStatus, self::STATUSES_DERIVED_FROM_SHIPMENTS, true) => ShippingStatus::IN_PROCESS,
            default => $storedStatus,
        };
    }
}
