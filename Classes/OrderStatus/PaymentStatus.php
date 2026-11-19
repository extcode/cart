<?php

declare(strict_types=1);

namespace Extcode\Cart\OrderStatus;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * The identifiers of the payment status registered by EXT:cart in
 * Configuration/OrderStatus.php.
 *
 * Other extensions register further payment status, all registered status
 * are returned by the OrderStatusRegistry.
 */
final class PaymentStatus
{
    public const OPEN = 'open';

    public const PENDING = 'pending';

    public const PAID = 'paid';

    public const CANCELED = 'canceled';
}
