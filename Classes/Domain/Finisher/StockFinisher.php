<?php

namespace Extcode\Cart\Domain\Finisher;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Stock Finisher
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class StockFinisher extends AbstractFinisher
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

        $this->stockUtility->handleStock($cart, $this->settings);
    }
}
