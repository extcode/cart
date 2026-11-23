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
use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Domain\Order\History\OrderChangeSource;
use Extcode\Cart\Domain\Order\Status\ServiceType;
use Psr\EventDispatcher\StoppableEventInterface;

/**
 * Dispatched by the StatusChangeService before the payment or the shipping
 * status of an order is changed. Listeners can veto the change with a reason.
 *
 * Use it for checks only, not for side effects: the change can still fail
 * afterwards, e.g. if another request changed the status in the meantime.
 * Side effects belong to the AfterStatusChangeEvent.
 */
final class BeforeStatusChangeEvent implements StoppableEventInterface
{
    private string $vetoReason = '';

    private bool $vetoed = false;

    public function __construct(
        private readonly Item $orderItem,
        private readonly AbstractService $service,
        private readonly ServiceType $serviceType,
        private readonly string $fromStatus,
        private readonly string $toStatus,
        private readonly OrderChangeSource $source,
        private readonly string $initiator,
        private readonly int $backendUser,
    ) {
    }

    public function getOrderItem(): Item
    {
        return $this->orderItem;
    }

    /**
     * The payment or the shipping.
     */
    public function getService(): AbstractService
    {
        return $this->service;
    }

    public function getServiceType(): ServiceType
    {
        return $this->serviceType;
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

    /**
     * Who or what triggers the change if not a backend user, e.g. "cart_paypal", empty if unknown.
     */
    public function getInitiator(): string
    {
        return $this->initiator;
    }

    /**
     * The uid of the backend user, 0 if there is none.
     */
    public function getBackendUser(): int
    {
        return $this->backendUser;
    }

    /**
     * Prevents the change, the reason is shown to the editor. The first veto
     * stops the propagation to the other listeners.
     */
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
