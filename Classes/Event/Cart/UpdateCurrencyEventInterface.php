<?php

namespace Extcode\Cart\Event\Cart;

use Extcode\Cart\Domain\Model\Cart\Cart;
use TYPO3\CMS\Extbase\Mvc\Request;

interface UpdateCurrencyEventInterface
{
    public function __construct(Cart $cart, Request $request, array $settings);

    public function getCart(): Cart;

    public function getRequest(): Request;

    public function getSettings(): array;
}
