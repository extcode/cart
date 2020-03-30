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

class CsvHeaderViewHelper extends AbstractViewHelper
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
        $delim = $this->arguments['delim'];
        $quote = $this->arguments['quote'];

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
