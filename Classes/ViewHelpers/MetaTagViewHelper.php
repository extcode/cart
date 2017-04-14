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
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Title Tag ViewHelper
 *
 * @author Georg Ringer <typo3@ringerge.org>
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class MetaTagViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{

    /**
     * @var string
     */
    protected $tagName = 'meta';

    /**
     * Arguments initialization
     */
    public function initializeArguments()
    {
        $this->registerTagAttribute('property', 'string', 'Property of meta tag');
        $this->registerTagAttribute('name', 'string', 'Content of meta tag using the name attribute');
        $this->registerTagAttribute('content', 'string', 'Content of meta tag');
    }

    /**
     * Renders a meta tag
     *
     * @param bool $useCurrentDomain If set, current domain is used
     * @param bool $forceAbsoluteUrl If set, absolute url is forced
     */
    public function render($useCurrentDomain = false, $forceAbsoluteUrl = false)
    {
        // set current domain
        if ($useCurrentDomain) {
            $this->tag->addAttribute('content', GeneralUtility::getIndpEnv('TYPO3_REQUEST_URL'));
        }

        // prepend current domain
        if ($forceAbsoluteUrl) {
            $path = $this->arguments['content'];
            if (!GeneralUtility::isFirstPartOfStr($path, GeneralUtility::getIndpEnv('TYPO3_SITE_URL'))) {
                $this->tag->addAttribute(
                    'content',
                    rtrim(GeneralUtility::getIndpEnv('TYPO3_SITE_URL'), '/')
                    . '/'
                    . ltrim($this->arguments['content'], '/')
                );
            }
        }

        if ($useCurrentDomain || (isset($this->arguments['content']) && !empty($this->arguments['content']))) {
            $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
            $pageRenderer->addMetaTag($this->tag->render());
        }
    }
}
