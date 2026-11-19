<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order\Status;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * The services of an order which have a status. The status values themselves
 * are no enum, extensions register their own ones in
 * Configuration/OrderStatus.php.
 */
enum ServiceType: string
{
    case Payment = 'payment';
    case Shipping = 'shipping';
}
