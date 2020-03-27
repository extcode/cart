<?php

namespace Extcode\Cart\ViewHelpers\Format;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class NothingViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{

    /**
     * Output is escaped already. We must not escape children, to avoid double encoding.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * Render children but do nothing else
     */
    public function render()
    {
        $this->renderChildren();
    }
}
