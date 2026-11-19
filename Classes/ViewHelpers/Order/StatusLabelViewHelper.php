<?php

declare(strict_types=1);

namespace Extcode\Cart\ViewHelpers\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\OrderStatus\OrderStatusRegistry;
use Extcode\Cart\OrderStatus\ServiceType;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Renders the label of a registered payment or shipping status. A status
 * which is not registered (anymore) is rendered as its identifier.
 *
 * <cart:order.statusLabel serviceType="payment" status="{orderItem.payment.status}"/>
 */
final class StatusLabelViewHelper extends AbstractViewHelper
{
    public function __construct(
        private readonly OrderStatusRegistry $orderStatusRegistry,
    ) {
    }

    public function initializeArguments(): void
    {
        $this->registerArgument('serviceType', 'string', 'The service type, "payment" or "shipping".', true);
        $this->registerArgument('status', 'string', 'The identifier of the status.', true);
    }

    public function render(): string
    {
        $serviceTypeValue = $this->arguments['serviceType'];
        assert(is_string($serviceTypeValue));
        $serviceType = ServiceType::from($serviceTypeValue);

        // e.g. null for an order without payment
        $identifier = $this->arguments['status'];
        if (!is_string($identifier) || $identifier === '') {
            return '';
        }

        return $this->orderStatusRegistry->getStatus($serviceType, $identifier)?->getLocalizedLabel() ?? $identifier;
    }
}
