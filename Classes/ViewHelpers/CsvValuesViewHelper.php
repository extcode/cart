<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\CsvUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class CsvValuesViewHelper extends AbstractViewHelper
{

    /**
     * Output is escaped already. We must not escape children, to avoid double encoding.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'orderItem',
            \Extcode\Cart\Domain\Model\Order\Item::class,
            'orderItem',
            true
        );
        $this->registerArgument(
            'delim',
            'string',
            'delim',
            false,
            ','
        );
        $this->registerArgument(
            'quote',
            'string',
            'quote',
            false,
            '"'
        );
    }

    /**
     * Format OrderItem to CSV format
     *
     * @return string
     */
    public function render()
    {
        $orderItem = $this->arguments['orderItem'];
        $delim = $this->arguments['delim'];
        $quote = $this->arguments['quote'];

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
