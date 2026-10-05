<?php

declare(strict_types=1);

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class EvalPrice
{
    /**
     * Returns Field JS
     */
    public function returnFieldJs(): string
    {
        return '
            var re = new RegExp("^[0-9]{1,}[.,]{0,1}[0-9]{0,2}$");

            if(value == "" || !value.match(re)) {
                alert("please enter a price");
                return "";
            }

            return value;';
    }

    /**
     * Evaluate Field Value
     */
    public function evaluateFieldValue($value, $is_in, &$set): string
    {
        if ($value == ''
            || $value == 'please enter a price'
            || !preg_match('/^[0-9]{1,}[.,]{0,1}[0-9]{0,2}$/', (string) $value)
        ) {
            return 'please enter a price';
        }

        return $value;
    }
}
