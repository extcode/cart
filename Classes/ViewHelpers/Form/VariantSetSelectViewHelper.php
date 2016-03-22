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
 * BeVariantAttributeSelect ViewHelper
 *
 * @package cart
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class BeVariantAttributeSelectViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * render
     *
     * @param string $name
     * @param \Extcode\Cart\Domain\Model\Product\BeVariantAttribute $productBeVariantAttribute
     * @return array
     */
    public function render($name = '', \Extcode\Cart\Domain\Model\Product\BeVariantAttribute $productBeVariantAttribute)
    {
        $out = '';

        $out .= '<select name="cart_productvariant_1">';

        foreach ($productBeVariantAttribute->getBeVariantAttributeOptions() as $beVariantAttributeOption) {
            $out .= '<option value="' . $beVariantAttributeOption->getUid() . '">' . $beVariantAttributeOption->getTitle() . '</option>';
        }

        $out .= '</select>';

        return $out;
    }
}
