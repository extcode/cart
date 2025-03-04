<?php

namespace Extcode\Cart\Event\Cart;

use Extcode\Cart\Domain\Model\Cart\Cart;
use TYPO3\CMS\Extbase\Mvc\RequestInterface;

interface UpdateCountryEventInterface
{
    public function __construct(Cart $cart, RequestInterface $request);

    public function getCart(): Cart;

    public function getRequest(): RequestInterface;
}
