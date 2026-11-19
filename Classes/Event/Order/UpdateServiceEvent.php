<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\AbstractService;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * @deprecated since cart 12.4, will be removed in cart 13.0, listen to the AfterStatusChangeEvent instead
 *
 * TODO: 13.0.0 Remove the UpdateServiceEvent, see Deprecation #840.
 */
final class UpdateServiceEvent implements StoppableEventInterface
{
    private bool $isPropagationStopped = false;

    public function __construct(
        private readonly AbstractService $service
    ) {
    }

    public function getService(): AbstractService
    {
        return $this->service;
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
