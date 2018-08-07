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
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class ActionViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Link\ActionViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('product', '\Extcode\Cart\Domain\Model\Cart\Product', 'product', false);
        $this->registerArgument('beVariant', '\Extcode\Cart\Domain\Model\Cart\BeVariant', 'beVariant', false);
    }

    /**
     * @return string Rendered link
     */
    public function render()
    {
        $fieldName = '';
        if ($this->arguments['product']) {
            $product = $this->arguments['product'];
            $fieldName = '[' . $product->getId() . ']';
        }
        if ($this->arguments['beVariant']) {
            $variant = $this->arguments['beVariant'];
            $fieldName = $this->getVariantFieldName($variant);
        }

        $additionalParams = (array)$this->arguments['additionalParams'];
        $additionalParams['tx_cart_cart[product]' . $fieldName] = 1;
        $this->arguments['additionalParams'] = $additionalParams;

        return parent::render();
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
