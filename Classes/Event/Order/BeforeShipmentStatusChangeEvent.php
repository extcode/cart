<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Fulfillment\Shipment;
use Extcode\Cart\Domain\Order\History\OrderChangeSource;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Use it for checks only, not for side effects: the change can still fail
 * afterwards, e.g. if another request changed the status in the meantime.
 * Side effects belong to the AfterShipmentStatusChangeEvent.
 */
final class BeforeShipmentStatusChangeEvent implements StoppableEventInterface
{
    private string $vetoReason = '';

    private bool $vetoed = false;

    public function __construct(
        private readonly Shipment $shipment,
        private readonly string $fromStatus,
        private readonly string $toStatus,
        private readonly OrderChangeSource $source,
        private readonly string $initiator,
        private readonly int $backendUserUid,
    ) {
    }

    public function getShipmentWithStoredStatus(): Shipment
    {
        return $this->shipment;
    }

    public function getFromStatus(): string
    {
        return $this->fromStatus;
    }

    public function getToStatus(): string
    {
        return $this->toStatus;
    }

    public function getSource(): OrderChangeSource
    {
        return $this->source;
    }

    public function getInitiator(): string
    {
        return $this->initiator;
    }

    public function getBackendUserUid(): int
    {
        return $this->backendUserUid;
    }

    public function veto(string $reason): void
    {
        $this->vetoed = true;
        $this->vetoReason = $reason;
    }

    public function isVetoed(): bool
    {
        return $this->vetoed;
    }

    public function getVetoReason(): string
    {
        return $this->vetoReason;
    }

    public function isPropagationStopped(): bool
    {
        return $this->vetoed;
    }
}
