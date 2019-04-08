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
