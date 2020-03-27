<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\CsvUtility;

class CsvHeaderViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
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
     * @param string $delim
     * @param string $quote
     * @return string
     */
    public function render($delim = ',', $quote = '"')
    {
        $orderItemArr = [];

        $orderItemArr[] = 'Order Number';
        $orderItemArr[] = 'Order Date';
        $orderItemArr[] = 'Invoice Number';
        $orderItemArr[] = 'Invoice Date';

        $orderItemArr[] = 'Salutation';
        $orderItemArr[] = 'Title';
        $orderItemArr[] = 'FirstName';
        $orderItemArr[] = 'LastName';

        return CsvUtility::csvValues($orderItemArr, $delim, $quote);
    }
}
