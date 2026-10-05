<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DateTime;
use Extcode\Cart\Event\Order\NumberGeneratorEventInterface;

class DeliveryNumber extends Number
{
    public function __invoke(NumberGeneratorEventInterface $event): void
    {
        $onlyGenerateNumberOfType = $event->getOnlyGenerateNumberOfType();
        if (!empty($onlyGenerateNumberOfType) && !in_array('delivery', $onlyGenerateNumberOfType, true)) {
            return;
        }

        $orderItem = $event->getOrderItem();

        $orderItem->setDeliveryNumber($this->generateNumber($event));
        $orderItem->setDeliveryDate(new DateTime());

        $this->orderItemRepository->update($orderItem);

        $this->persistenceManager->persistAll();
    }

    protected function getRegistryName(NumberGeneratorEventInterface $event): string
    {
        return 'lastDelivery_' . $event->getOrderItem()->getCartPid();
    }
}
