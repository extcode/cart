<?php

declare(strict_types=1);

namespace Extcode\Cart\Event\Cart;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\Cart;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

final readonly class UpdateCurrencyEvent implements UpdateCurrencyEventInterface
{
    public function __construct(
        private Cart $cart,
        private RequestInterface $request,
        private array $settings = []
    ) {}

    public function getCart(): Cart
    {
        return $this->cart;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getSettings(): array
    {
        return $this->settings;
    }
}
