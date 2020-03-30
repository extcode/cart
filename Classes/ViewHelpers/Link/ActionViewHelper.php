<?php

namespace Extcode\Cart\ViewHelpers\Link;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class ActionViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Link\ActionViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'product',
            \Extcode\Cart\Domain\Model\Cart\Product::class,
            'product',
            false
        );
        $this->registerArgument(
            'beVariant',
            \Extcode\Cart\Domain\Model\Cart\BeVariant::class,
            'beVariant',
            false
        );
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
