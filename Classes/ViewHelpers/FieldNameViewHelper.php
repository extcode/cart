<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class FieldNameViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'product',
            \Extcode\Cart\Domain\Model\Cart\Product::class,
            'product',
            false,
            0
        );
        $this->registerArgument(
            'variant',
            \Extcode\Cart\Domain\Model\Cart\BeVariant::class,
            'variant',
            false,
            0
        );
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
