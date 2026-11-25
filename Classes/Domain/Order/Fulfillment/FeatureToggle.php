<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Order\Fulfillment;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Disabled by default. Once shipments exist, the feature should not be
 * disabled again.
 */
final class FeatureToggle
{
    public const NAME = 'cart.orderFulfillment';
}
