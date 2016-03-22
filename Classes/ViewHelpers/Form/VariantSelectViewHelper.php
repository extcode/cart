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
     * @param string $name
     * @param \Extcode\Cart\Domain\Model\Product\Product $product
     * @return array
     */
    public function render($name = '', \Extcode\Cart\Domain\Model\Product\Product $product)
    {
        $out = '';

        $out .= '<select name="' . $name . '">';

        foreach ($product->getBeVariants() as $beVariant) {
            /**
             * @var \Extcode\Cart\Domain\Model\Product\BeVariant $beVariant
             */

            $optionLabelArray = array();
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

            $out .= '<option value="' . $beVariant->getUid() . '">' . $optionLabel . '</option>';
        }

        $out .= '</select>';

        return $out;
    }
}
