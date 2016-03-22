<?php

namespace Extcode\Cart\ViewHelpers\Link;

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
 * Action ViewHelper
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ActionViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Link\ActionViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('product', '\Extcode\Cart\Domain\Model\Cart\Product', 'product', false, 0);
        $this->registerArgument('beVariant', '\Extcode\Cart\Domain\Model\Cart\BeVariant', 'beVariant', false, 0);
    }

    /**
     * @param string $action Target action
     * @param array $arguments Arguments
     * @param string $controller Target controller. If NULL current controllerName is used
     * @param string $extensionName Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used
     * @param string $pluginName Target plugin. If empty, the current plugin name is used
     * @param integer $pageUid target page. See TypoLink destination
     * @param integer $pageType type of the target page. See typolink.parameter
     * @param boolean $noCache set this to disable caching for the target page. You should not need this.
     * @param boolean $noCacheHash set this to supress the cHash query parameter created by TypoLink. You should not need this.
     * @param string $section the anchor to be added to the URI
     * @param string $format The requested format, e.g. ".html
     * @param boolean $linkAccessRestrictedPages If set, links pointing to access restricted pages will still link to the page even though the page cannot be accessed.
     * @param array $additionalParams additional query parameters that won't be prefixed like $arguments (overrule $arguments)
     * @param boolean $absolute If set, the URI of the rendered link is absolute
     * @param boolean $addQueryString If set, the current query parameters will be kept in the URI
     * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the URI. Only active if $addQueryString = true
     * @param string $addQueryStringMethod Set which parameters will be kept. Only active if $addQueryString = true
     * @return string Rendered link
     */
    public function render(
        $action = null,
        array $arguments = array(),
        $controller = null,
        $extensionName = null,
        $pluginName = null,
        $pageUid = null,
        $pageType = 0,
        $noCache = false,
        $noCacheHash = false,
        $section = '',
        $format = '',
        $linkAccessRestrictedPages = false,
        array $additionalParams = array(),
        $absolute = false,
        $addQueryString = false,
        array $argumentsToBeExcludedFromQueryString = array(),
        $addQueryStringMethod = null
    ) {

        $fieldName = '';
        if ($this->arguments['product']) {
            $product = $this->arguments['product'];
            $fieldName = '[' . $product->getId() . ']';
        }
        if ($this->arguments['beVariant']) {
            $variant = $this->arguments['beVariant'];
            $fieldName = $this->getVariantFieldName($variant);
        }

        $additionalParams['tx_cart_cart[product]' . $fieldName] = 1;

        return parent::render($action, $arguments, $controller, $extensionName, $pluginName, $pageUid, $pageType,
            $noCache, $noCacheHash, $section, $format, $linkAccessRestrictedPages, $additionalParams, $absolute,
            $addQueryString, $argumentsToBeExcludedFromQueryString, $addQueryStringMethod);
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Cart\BeVariant $variant
     *
     * @return string
     */
    protected function getVariantFieldName($variant)
    {
        $fieldName = '';

        if ($variant->getParentBeVariant()) {
            $fieldName .= $this->getVariantFieldName($variant->getParentBeVariant());
        }
        if ($variant->getProduct()) {
            $fieldName .= '[' . $variant->getProduct()->getId() . ']';
        }

        $fieldName .= '[' . $variant->getId() . ']';

        return $fieldName;
    }
}
