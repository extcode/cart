<?php

declare(strict_types=1);

namespace Extcode\Cart\Widgets;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

/**
 * Data provider of the NumberWidget for amounts, which are shown with currency.
 */
interface AmountDataProviderInterface
{
    /**
     * Returns the amount shown in the widget.
     */
    public function getAmount(): float;
}
