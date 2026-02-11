<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Session;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\AddressInterface;
use Psr\EventDispatcher\StoppableEventInterface;

final class AfterRestoreAddressEvent implements StoppableEventInterface
{
    private bool $isPropagationStopped = false;

    public function __construct(private AddressInterface $address) {}

    public function getAddress(): AddressInterface
    {
        return $this->address;
    }

    public function setCart(AddressInterface $address): void
    {
        $this->address = $address;
    }

    public function setPropagationStopped(bool $isPropagationStopped): void
    {
        $this->isPropagationStopped = $isPropagationStopped;
    }

    public function isPropagationStopped(): bool
    {
        return $this->isPropagationStopped;
    }
}
