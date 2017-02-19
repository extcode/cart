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
     * @return void
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

        $arguments = array(
            array('tx_cart_product' =>
                array(
                    'controller' => 'Product',
                    'action' => 'show',
                    'product' => $product->getUid()
                )
            )
        );

        $uriBuilder = $this->controllerContext->getUriBuilder();
        $canonicalUrl = $uriBuilder->reset()
            ->setTargetPageUid($pageUid)
            ->setCreateAbsoluteUri(true)
            ->setArguments($arguments)
            ->build();

        $this->tag->addAttribute('rel', 'canonical');
        $this->tag->addAttribute('href', $canonicalUrl);
        $GLOBALS['TSFE']->getPageRenderer()->addHeaderData($this->tag->render());
    }
}
