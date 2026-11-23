<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\History;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * The types of the changes of an order written by EXT:cart. Other extensions
 * write their own types, the type is a plain string.
 */
final class OrderChangeType
{
    public const PAYMENT_STATUS = 'payment_status';

    public const SHIPPING_STATUS = 'shipping_status';
}
