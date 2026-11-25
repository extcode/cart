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
 * The parts of an order with a status. The status are registered in
 * Configuration/OrderStatus.php.
 */
enum ServiceType: string
{
    case Payment = 'payment';
    case Shipping = 'shipping';
    case Shipment = 'shipment';
}
