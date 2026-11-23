<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Status;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * The identifiers of the shipping status registered by EXT:cart in
 * Configuration/OrderStatus.php.
 *
 * Other extensions register further shipping status, all registered status
 * are returned by the OrderStatusRegistry.
 */
final class ShippingStatus
{
    public const OPEN = 'open';

    public const ON_HOLD = 'on_hold';

    public const IN_PROCESS = 'in_process';

    public const SHIPPED = 'shipped';
}
