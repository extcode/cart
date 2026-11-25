<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * The shipment status of EXT:cart, other extensions register further ones.
 */
final class ShipmentStatus
{
    public const OPEN = 'open';

    public const IN_PROCESS = 'in_process';

    public const SHIPPED = 'shipped';

    public const DELIVERED = 'delivered';

    public const CANCELED = 'canceled';
}
