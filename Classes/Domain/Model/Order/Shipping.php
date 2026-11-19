<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\OrderStatus\ShippingStatus;

class Shipping extends AbstractService
{
    public function __construct()
    {
        $this->status = ShippingStatus::OPEN;
    }
}
