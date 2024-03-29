<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Order\Item;
use Psr\EventDispatcher\StoppableEventInterface;

final class AttachementEvent implements StoppableEventInterface
{
    private array $attachments = [];

    private bool $isPropagationStopped = false;

    public function __construct(private string $type, private ?Item $orderItem = null) {}

    public function getType(): string
    {
        return $this->type;
    }

    public function getOrderItem(): ?Item
    {
        return $this->orderItem;
    }

    public function setOrderItem(Item $orderItem): void
    {
        $this->orderItem = $orderItem;
    }

    /**
     * @return string[]
     */
    public function getAttachments(): array
    {
        return $this->attachments;
    }

    public function addAttachment(string $attachment): void
    {
        $this->attachments[] = $attachment;
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
