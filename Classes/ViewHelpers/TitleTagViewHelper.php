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

/**
 * Title Tag ViewHelper
 *
 * @author Georg Ringer <typo3@ringerge.org>
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class TitleTagViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Override the title tag
     */
    public function render()
    {
        $content = trim($this->renderChildren());
        if (!empty($content)) {
            $GLOBALS['TSFE']->altPageTitle = $content;
            $GLOBALS['TSFE']->indexedDocTitle = $content;
        }
    }
}
