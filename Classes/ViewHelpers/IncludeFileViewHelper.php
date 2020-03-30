<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class IncludeFileViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'path',
            'string',
            'Path to the CSS/JS file which should be included',
            true
        );
        $this->registerArgument(
            'compress',
            'bool',
            'Define if file should be compressed',
            false,
            false
        );
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
