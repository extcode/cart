<?php

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Cart\UpdateCurrencyEvent;
use Extcode\Cart\Service\SessionHandler;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Extbase\Mvc\Request;

class CurrencyUtility
{
    public function __construct(
        protected EventDispatcherInterface $eventDispatcher,
        protected SessionHandler $sessionHandler
    ) {}

    public function updateCurrency(array $cartSettings, array $pluginSettings, Request $request): void
    {
        $cart = $this->sessionHandler->restoreCart($cartSettings['pid']);

        $event = new UpdateCurrencyEvent($cart, $request, $pluginSettings['settings']['currencies']);
        $this->eventDispatcher->dispatch($event);

        $this->sessionHandler->writeCart($cartSettings['pid'], $event->getCart());
    }
}
