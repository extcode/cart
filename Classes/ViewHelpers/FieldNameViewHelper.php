<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Cart\BeVariant;
use Extcode\Cart\Domain\Model\Cart\Product;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class FieldNameViewHelper extends AbstractViewHelper
{
    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'product',
            Product::class,
            'product',
            false,
            0
        );
        $this->registerArgument(
            'variant',
            BeVariant::class,
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

    protected function getVariantFieldName(BeVariant $variant): string
    {
        $fieldName = '';

        if ($variant->getParent() instanceof BeVariant) {
            $fieldName .= $this->getVariantFieldName($variant->getParent());
        }

        if ($variant->getParent() instanceof Product) {
            $fieldName .= '[' . $variant->getParent()->getId() . ']';
        }

        $fieldName .= '[' . $variant->getId() . ']';

        return $fieldName;
    }
}
