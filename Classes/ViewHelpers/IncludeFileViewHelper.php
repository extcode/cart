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
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * ViewHelper to include a css/js file
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class IncludeFileViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('path', 'string', 'Path to the CSS/JS file which should be included', true);
        $this->registerArgument('compress', 'bool', 'Define if file should be compressed', false, false);
    }

    /**
     * Include a CSS/JS file
     */
    public function render()
    {
        $path = $this->arguments['path'];
        $compress = $this->arguments['compress'];

        $pageRenderer = GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Page\\PageRenderer');
        if (TYPO3_MODE === 'FE') {
            $path = $GLOBALS['TSFE']->tmpl->getFileName($path);
        }

        if (strtolower(substr($path, -3)) === '.js') {
            $pageRenderer->addJsFile($path, null, $compress);
        } elseif (strtolower(substr($path, -4)) === '.css') {
            $pageRenderer->addCssFile($path, 'stylesheet', 'all', '', $compress);
        }
    }
}
