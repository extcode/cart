<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Document;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use Psr\EventDispatcher\StoppableEventInterface;

final class GenerateDocumentEvent implements StoppableEventInterface
{
    private bool $isPropagationStopped = false;

    public function __construct(
        private readonly OrderItem $orderItem,
        private readonly string $type
    ) {
    }

    public function getOrderItem(): OrderItem
    {
        return $this->orderItem;
    }

    public function getType(): string
    {
        return $this->type;
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
