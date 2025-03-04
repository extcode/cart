<?php

namespace Extcode\Cart\Event\Cart;

use Extcode\Cart\Domain\Model\Cart\Cart;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

interface UpdateCurrencyEventInterface
{
    public function __construct(Cart $cart, RequestInterface $request, array $settings);

    public function getCart(): Cart;

    public function getRequest(): RequestInterface;

    public function getSettings(): array;
}
