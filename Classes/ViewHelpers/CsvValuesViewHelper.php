<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\CsvUtility;

class CsvValuesViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * Output is escaped already. We must not escape children, to avoid double encoding.
     *
     * @var bool
     */
    protected $escapeOutput = false;

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

        $orderItemArr[] = $orderItem->getOrderNumber();
        $orderItemArr[] = $orderItem->getOrderDate() ? $orderItem->getOrderDate()->format('d.m.Y') : '';
        $orderItemArr[] = $orderItem->getInvoiceNumber();
        $orderItemArr[] = $orderItem->getInvoiceDate() ? $orderItem->getInvoiceDate()->format('d.m.Y') : '';

        $orderItemArr[] = $orderItem->getBillingAddress()->getSalutation();
        $orderItemArr[] = $orderItem->getBillingAddress()->getTitle();
        $orderItemArr[] = $orderItem->getBillingAddress()->getFirstName();
        $orderItemArr[] = $orderItem->getBillingAddress()->getLastName();

        return CsvUtility::csvValues($orderItemArr, $delim, $quote);
    }
}
