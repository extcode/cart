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

final class ProcessOrderCheckStockEvent
{
    private bool $everyProductAvailable = true;

    /**
     * @var FlashMessage[]
     */
    protected array $insufficientStockMessages = [];

    public function __construct(
        private readonly Cart $cart
    ) {}

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function isEveryProductAvailable(): bool
    {
        return $this->everyProductAvailable;
    }

    public function setNotEveryProductAvailable(): void
    {
        $this->everyProductAvailable = false;
    }

    /**
     * @return FlashMessage[]
     */
    public function getInsufficientStockMessages(): array
    {
        return $this->insufficientStockMessages;
    }

    public function addInsufficientStockMessage(FlashMessage $insufficientStockMessage): void
    {
        $this->insufficientStockMessages[] = $insufficientStockMessage;
    }
}
