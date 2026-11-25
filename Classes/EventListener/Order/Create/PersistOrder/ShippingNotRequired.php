<?php

declare(strict_types=1);

namespace Extcode\Cart\EventListener\Order\Create\PersistOrder;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Order\Fulfillment\FeatureToggle as FulfillmentFeatureToggle;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;
use Extcode\Cart\Domain\Repository\Order\ShippingRepository;
use Extcode\Cart\Event\Order\PersistOrderEvent;
use TYPO3\CMS\Core\Configuration\Features;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

final readonly class ShippingNotRequired
{
    public function __construct(
        private Features $features,
        private ShippingRepository $shippingRepository,
        private PersistenceManager $persistenceManager,
    ) {
    }

    public function __invoke(PersistOrderEvent $event): void
    {
        if (!$this->features->isFeatureEnabled(FulfillmentFeatureToggle::NAME)) {
            return;
        }

        $orderItem = $event->getOrderItem();
        $shipping = $orderItem->getShipping();
        if ($shipping === null) {
            return;
        }

        foreach ($orderItem->getProducts() as $orderProduct) {
            if ($orderProduct->isShippable()) {
                return;
            }
        }

        $shipping->setStatus(ShippingStatus::NOT_REQUIRED);
        $this->shippingRepository->update($shipping);
        $this->persistenceManager->persistAll();
    }
}
