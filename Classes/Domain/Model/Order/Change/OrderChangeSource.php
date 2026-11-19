<?php

declare(strict_types=1);

namespace Extcode\Cart\Domain\Model\Order\Change;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Where a change of an order comes from. Other extensions may use their own
 * sources, the source is a plain string.
 */
final class OrderChangeSource
{
    /**
     * An editor in the backend, e.g. in the order details of the backend module.
     */
    public const BACKEND = 'backend';

    /**
     * A request in the frontend, e.g. the notification of a payment provider.
     */
    public const FRONTEND = 'frontend';

    /**
     * A command or a scheduler task.
     */
    public const CLI = 'cli';
}
