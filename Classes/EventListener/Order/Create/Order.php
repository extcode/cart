<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Order\CreateEvent;
use Extcode\Cart\Event\Order\PersistOrderEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

class Order
{
    public function __construct(
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly PersistenceManager $persistenceManager
    ) {}

    public function __invoke(CreateEvent $event): void
    {
        $settings = $event->getSettings();
        $cart = $event->getCart();
        $orderItem = $event->getOrderItem();

        $event = new PersistOrderEvent($cart, $orderItem, $settings);
        $this->eventDispatcher->dispatch($event);

        $this->persistenceManager->persistAll();
    }
}
