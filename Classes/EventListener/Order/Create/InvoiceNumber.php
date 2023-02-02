<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Event\Order\NumberGeneratorEventInterface;

class InvoiceNumber extends Number
{
    protected function getRegistryName(NumberGeneratorEventInterface $event): string
    {
        return 'lastInvoice' . '_' . $event->getOrderItem()->getCartPid();
    }

    public function __invoke(NumberGeneratorEventInterface $event): void
    {
        $onlyGenerateNumberOfType = $event->getOnlyGenerateNumberOfType();
        if (!empty($onlyGenerateNumberOfType) && !in_array('invoice', $onlyGenerateNumberOfType, true)) {
            return;
        }

        $orderItem = $event->getOrderItem();

        $orderItem->setInvoiceNumber($this->generateNumber($event));
        $orderItem->setInvoiceDate(new \DateTime());

        $this->orderItemRepository->update($orderItem);

        $this->persistenceManager->persistAll();
    }
}
