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
use Psr\EventDispatcher\StoppableEventInterface;
use TYPO3\CMS\Extbase\Mvc\Request;

interface RetrieveProductsFromRequestEventInterface extends StoppableEventInterface
{
    public function __construct(Request $request, Cart $cart);

    public function getCart(): Cart;

    public function getRequest(): Request;

    public function getProducts(): array;

    public function getErrors(): array;
}
