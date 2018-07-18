<?php

namespace Extcode\Cart\ViewHelpers;

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
use TYPO3\CMS\Core\Utility\CsvUtility;

/**
 * Format array of values to CSV format
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CsvValuesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Format OrderItem to CSV format
     *
     * @param \Extcode\Cart\Domain\Model\Order\Item $orderItem Order Item
     * @param string $delim Delimiter
     * @param string $quote Quote Style
     *
     * @return string
     */
    public function render(\Extcode\Cart\Domain\Model\Order\Item $orderItem, $delim = ',', $quote = '"')
    {
        $orderItemArr = [];

        $orderItemArr[] = $orderItem->getBillingAddress()->getSalutation();
        $orderItemArr[] = $orderItem->getBillingAddress()->getTitle();
        $orderItemArr[] = $orderItem->getBillingAddress()->getFirstName();
        $orderItemArr[] = $orderItem->getBillingAddress()->getLastName();
        $orderItemArr[] = $orderItem->getOrderNumber();
        $orderItemArr[] = $orderItem->getInvoiceNumber();

        return CsvUtility::csvValues($orderItemArr, $delim, $quote);
    }
}
