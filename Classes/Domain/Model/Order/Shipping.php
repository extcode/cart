<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class Shipping extends AbstractService
{
    public function isOpen(): bool
    {
        return $this->getStatus() === 'open';
    }

    public function isOnHold(): bool
    {
        return $this->getStatus() === 'on_hold';
    }

    public function isInProcess(): bool
    {
        return $this->getStatus() === 'in_process';
    }

    public function isShipped(): bool
    {
        return $this->getStatus() === 'shipped';
    }
}
