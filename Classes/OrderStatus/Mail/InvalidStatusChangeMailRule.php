<?php

declare(strict_types=1);

namespace Extcode\Cart\OrderStatus\Mail;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * A rule of plugin.tx_cart.statusChange.mail.<serviceType>.<key> which can not be used.
 */
final readonly class InvalidStatusChangeMailRule
{
    public function __construct(
        public string $key,
        public string $reason,
    ) {
    }
}
