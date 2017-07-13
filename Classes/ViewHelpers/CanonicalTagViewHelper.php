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
 * adds the canonical tag to header data
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class CanonicalTagViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'link';

    /**
     * Override the canonical tag
     *
     * @param \Extcode\Cart\Domain\Model\Product\Product $product
     */
    public function render(\Extcode\Cart\Domain\Model\Product\Product $product)
    {
        /* get topic category */
        $category = $product->getMainCategory();

        if (!$category) {
            return;
        }

        $pageUid = $category->getCartProductSinglePid();

        if (!$pageUid) {
            return;
        }

        $arguments = [
            ['tx_cart_product' =>
                [
                    'controller' => 'Product',
                    'product' => $product->getUid()
                ]
            ]
        ];

        $uriBuilder = $this->controllerContext->getUriBuilder();
        $canonicalUrl = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setCreateAbsoluteUri(true)
            ->setArguments($arguments)
            ->build();

        $this->tag->addAttribute('rel', 'canonical');
        $this->tag->addAttribute('href', $canonicalUrl);
        $this->getPageRenderer()->addHeaderData($this->tag->render());
    }

    /**
     * @return \TYPO3\CMS\Core\Page\PageRenderer
     */
    protected function getPageRenderer()
    {
        if ('FE' === TYPO3_MODE && is_callable([$this->getTypoScriptFrontendController(), 'getPageRenderer'])) {
            return $this->getTypoScriptFrontendController()->getPageRenderer();
        } else {
            return GeneralUtility::makeInstance('TYPO3\CMS\Core\Page\PageRenderer');
        }
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
