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
use TYPO3\CMS\Core\Messaging\FlashMessage;

final class ProcessOrderCheckStockEvent implements ProcessOrderCheckStockEventInterface
{
    private bool $allProductsAvailable = true;

    public function __construct(
        private readonly Cart $cart
    ) {}

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function allProductsAreAvailable(): bool
    {
        return $this->allProductsAvailable;
    }

    public function setNotAllProductsAreAvailable(): void
    {
        $this->allProductsAvailable = false;
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
