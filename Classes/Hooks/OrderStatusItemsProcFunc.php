<?php

declare(strict_types=1);

namespace Extcode\Cart\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Configuration\OrderStatus\OrderStatusRegistry;
use Extcode\Cart\Domain\Model\Order\Status\ServiceType;
use Symfony\Component\DependencyInjection\Attribute\Autoconfigure;

/**
 * Sets the registered status as items of the status field of the payment and
 * the shipping, the service type is given in the itemsProcConfig of the TCA.
 */
#[Autoconfigure(public: true)]
final readonly class OrderStatusItemsProcFunc
{
    public function __construct(
        private OrderStatusRegistry $orderStatusRegistry,
    ) {
    }

    /**
     * @param array{items: array<array-key, mixed>, config: array{itemsProcConfig: array{serviceType: string}}} $parameters
     */
    public function addItems(array &$parameters): void
    {
        $serviceType = ServiceType::from($parameters['config']['itemsProcConfig']['serviceType']);

        $items = [];
        foreach ($this->orderStatusRegistry->getStatuses($serviceType) as $status) {
            $items[] = [
                'label' => $status->label,
                'value' => $status->identifier,
            ];
        }

        // items of the TCA are part of the registered status already (deprecated), e.g. ones of page TSconfig are kept
        foreach ($parameters['items'] as $item) {
            $value = is_array($item) ? ($item['value'] ?? null) : null;
            if (!is_string($value) || !$this->orderStatusRegistry->hasStatus($serviceType, $value)) {
                $items[] = $item;
            }
        }

        $parameters['items'] = $items;
    }
}
