<?php

declare(strict_types=1);

namespace Extcode\Cart\Event;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use Extcode\Cart\Domain\Model\Cart\Product;
use TYPO3\CMS\Core\Messaging\FlashMessage;

class CheckProductAvailabilityEvent implements CheckProductAvailabilityEventInterface
{
    protected bool $available = true;

    /**
     * @var FlashMessage[]
     */
    protected array $messages = [];

    public function __construct(
        private readonly Cart $cart,
        private readonly Product $product,
        private readonly mixed $quantity,
        private readonly string $mode = 'update'
    ) {
    }

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getProduct(): Product
    {
        return $this->product;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function getMode(): string
    {
        return $this->mode;
    }

    public function isAvailable(): bool
    {
        return $this->available;
    }

    public function setAvailable(bool $available): void
    {
        $this->available = $available;
    }

    /**
     * @return FlashMessage[]
     */
    public function getMessages(): array
    {
        return $this->messages;
    }

    /**
     * @param FlashMessage[] $messages
     */
    public function setMessages(array $messages): void
    {
        $this->messages = $messages;
    }

    /**
     * @param FlashMessage $message
     */
    public function addMessage(FlashMessage $message): void
    {
        $this->messages[] = $message;
    }
}
