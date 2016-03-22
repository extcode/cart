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
 * FieldName ViewHelper
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class FieldNameViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Link\ActionViewHelper
{

    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument('product', '\Extcode\Cart\Domain\Model\Cart\Product', 'product', false, 0);
        $this->registerArgument('variant', '\Extcode\Cart\Domain\Model\Cart\BeVariant', 'variant', false, 0);
    }

    public function render()
    {
        $fieldName = '';

        if ($this->arguments['product']) {
            $product = $this->arguments['product'];
            $fieldName = '[' . $product->getId() . ']';
        }
        if ($this->arguments['variant']) {
            $variant = $this->arguments['variant'];
            $fieldName = $this->getVariantFieldName($variant);
        }

        return $fieldName;
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
