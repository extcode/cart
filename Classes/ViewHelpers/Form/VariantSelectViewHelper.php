<?php

namespace Extcode\Cart\ViewHelpers\Form;

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
 * VariantSelect ViewHelper
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class VariantSelectViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * render
     *
     * @param string $id
     * @param string $name
     * @param \Extcode\Cart\Domain\Model\Product\Product $product
     *
     * @return string
     */
    public function render($id = '', $name = '', \Extcode\Cart\Domain\Model\Product\Product $product)
    {
        $currencyViewHelper = $this->objectManager->get(
            \Extcode\Cart\ViewHelpers\Format\CurrencyViewHelper::class
        );
        $currencyViewHelper->initialize();
        $currencyViewHelper->setRenderingContext($this->renderingContext);

        $out = '';

        $out .= '<select id="' . $id . '" name="' . $name . '">';

        foreach ($product->getBeVariants() as $beVariant) {
            /**
             * @var \Extcode\Cart\Domain\Model\Product\BeVariant $beVariant
             */

            $currencyViewHelper->setRenderChildrenClosure(
                function () use ($beVariant) {
                    return $beVariant->getPriceCalculated();
                }
            );
            $beVariantPriceCalculated = $currencyViewHelper->render();

            $optionLabelArray = [];
            if ($product->getBeVariantAttribute1()) {
                $optionLabelArray[] = $beVariant->getBeVariantAttributeOption1()->getTitle();
            }
            if ($product->getBeVariantAttribute2()) {
                $optionLabelArray[] = $beVariant->getBeVariantAttributeOption2()->getTitle();
            }
            if ($product->getBeVariantAttribute3()) {
                $optionLabelArray[] = $beVariant->getBeVariantAttributeOption3()->getTitle();
            }
            $optionLabel = join(' - ', $optionLabelArray);

            $out .= '<option value="' . $beVariant->getUid() . '" data-price="' . $beVariantPriceCalculated . '">' . $optionLabel . '</option>';
        }

        $out .= '</select>';

        return $out;
    }
}
