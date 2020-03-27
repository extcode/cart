<?php

namespace Extcode\Cart\Domain\Finisher\Order;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class StockFinisher extends \Extcode\Cart\Domain\Finisher\AbstractFinisher
{
    /**
     * Stock Utility
     *
     * @var \Extcode\Cart\Utility\StockUtility
     */
    protected $stockUtility;

    /**
     * @param \Extcode\Cart\Utility\StockUtility $stockUtility
     */
    public function injectStockUtility(
        \Extcode\Cart\Utility\StockUtility $stockUtility
    ) {
        $this->stockUtility = $stockUtility;
    }

    public function executeInternal()
    {
        $cart = $this->finisherContext->getCart();

        $this->stockUtility->handleStock($cart);
    }
}
