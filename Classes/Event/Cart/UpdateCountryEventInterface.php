<?php

namespace Extcode\Cart\Event\Cart;

use Extcode\Cart\Domain\Model\Cart\Cart;
use TYPO3\CMS\Extbase\Mvc\Request;

interface UpdateCountryEventInterface
{
    public function __construct(Cart $cart, Request $request);

    public function getCart(): Cart;

    public function getRequest(): Request;
}
