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

interface CheckProductAvailabilityEventInterface
{
    public function __construct(Cart $cart, Product $product, $quantity, string $mode = 'update');

    public function getCart(): Cart;

    public function getProduct(): Product;

    public function getQuantity();

    public function getMode(): string;

    public function isAvailable(): bool;

    /**
     * @return FlashMessage[]
     */
    public function getMessages(): array;
}
