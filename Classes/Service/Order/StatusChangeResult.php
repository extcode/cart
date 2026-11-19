<?php

declare(strict_types=1);

namespace Extcode\Cart\Service\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * The result of StatusChangeService::change().
 */
enum StatusChangeResult
{
    /**
     * The status was changed and the change was written to the order history.
     */
    case Changed;

    /**
     * The order has this status already, nothing was changed.
     */
    case Unchanged;

    /**
     * The status of the order is not the expected one anymore, e.g. because
     * it was changed in another tab, nothing was changed.
     */
    case Stale;

    /**
     * The new status is not registered, nothing was changed.
     */
    case UnknownStatus;
}
