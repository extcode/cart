<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\MetaTag\MetaTagManagerRegistry;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ViewHelper to render meta tags
 *
 * # Example: Basic Example: product title as og:title meta tag
 * <code>
 * <cart:metaTag property="og:title" content="{product.title}" />
 * </code>
 * <output>
 * <meta property="og:title" content="TYPO3 is awesome" />
 * </output>
 */
class MetaTagViewHelper extends AbstractViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'meta';

    public function initializeArguments()
    {
        $this->registerArgument(
            'property',
            'string',
            'Property of meta tag',
            true
        );
        $this->registerArgument(
            'content',
            'string',
            'Content of meta tag',
            true
        );
    }

    public function render()
    {
        $metaTagManager = GeneralUtility::makeInstance(MetaTagManagerRegistry::class)
            ->getManagerForProperty($this->arguments['property']);
        $metaTagManager->addProperty(
            $this->arguments['property'],
            $this->arguments['content']
        );
    }
}
