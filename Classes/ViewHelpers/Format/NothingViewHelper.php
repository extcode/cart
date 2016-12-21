<?php

namespace Extcode\Cart\ViewHelpers\Format;

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
 * Nothing ViewHelper
 *
 * @package cart
 * @author Georg Ringer <typo3@ringerge.org>
 * @author Daniel Lorenz <ext.cart@extco.de>
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
     *
     * @return void
     */
    public function render()
    {
        $this->renderChildren();
    }
}
