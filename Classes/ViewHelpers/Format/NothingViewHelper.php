<?php

declare(strict_types=1);

namespace Extcode\Cart\ViewHelpers\Format;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class NothingViewHelper extends AbstractViewHelper
{
    /**
     * Output is escaped already. We must not escape children, to avoid double encoding.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    public function render(): void
    {
        $this->renderChildren();
    }
}

// keep compatibility for $escapeOutput property definition of parent class
// @php-cs-fixer-ignore phpdoc_to_property_type
