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
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class VariantSelectViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * Output is escaped already. We must not escape children, to avoid double encoding.
     *
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @var \Extcode\Cart\Domain\Model\Product\Product
     */
    protected $product = null;

    /**
     * Initialize arguments.
     *
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'product',
            \Extcode\Cart\Domain\Model\Product\Product::class,
            'product for select options',
            true
        );
        $this->registerArgument('id', 'string', 'id for select');
        $this->registerArgument('class', 'string', 'class for select');
        $this->registerArgument('name', 'string', 'name for select');
        $this->registerArgument('blank', 'string', 'blank adds blank option');
        $this->registerArgument('required', 'bool', 'required adds html5 required', false, true);
    }

    /**
     * render
     *
     * @return string
     */
    public function render()
    {
        $this->product = $this->arguments['product'];

        $select = [];

        if ($this->hasArgument('id')) {
            $select[] = 'id="' . $this->arguments['id'] . '" ';
        }
        if ($this->hasArgument('class')) {
            $select[] = 'class="' . $this->arguments['class'] . '" ';
        }
        if ($this->hasArgument('name')) {
            $select[] = 'name="' . $this->arguments['name'] . '" ';
        }
        if ($this->hasArgument('required')) {
            $select[] = 'required ';
        }

        $out = '<select ' . implode(' ', $select) . '>';

        if ($this->hasArgument('blank')) {
            $out .= '<option value="">' . $this->arguments['blank'] . '</option>';
        }

        $options = $this->getOptions();

        foreach ($options as $option) {
            $out .= $option;
        }

        $out .= '</select>';

        return $out;
    }

    /**
     * @return array
     */
    protected function getOptions()
    {
        $options = [];

        $currencyViewHelper = $this->objectManager->get(
            \Extcode\Cart\ViewHelpers\Format\CurrencyViewHelper::class
        );
        $currencyViewHelper->initialize();
        $currencyViewHelper->setRenderingContext($this->renderingContext);

        foreach ($this->product->getBeVariants() as $beVariant) {
            /**
             * @var \Extcode\Cart\Domain\Model\Product\BeVariant $beVariant
             */
            $currencyViewHelper->setRenderChildrenClosure(
                function () use ($beVariant) {
                    return $beVariant->getPriceCalculated();
                }
            );
            $regularPrice = $currencyViewHelper->render();

            $currencyViewHelper->setRenderChildrenClosure(
                function () use ($beVariant) {
                    return $beVariant->getBestPriceCalculated();
                }
            );
            $specialPrice = $currencyViewHelper->render();

            $specialPricePercentageDiscount = number_format($beVariant->getBestSpecialPricePercentageDiscount(), 2);

            $optionLabel = $this->getOptionLabel($beVariant);

            $value = 'value="' . $beVariant->getUid() . '"';
            $data = 'data-regular-price="' . $regularPrice . '"';
            if ($regularPrice != $specialPrice) {
                $data .= ' data-special-price="' . $specialPrice . '"';
                $data .= ' data-special-price-percentage-discount="' . $specialPricePercentageDiscount . '"';
            }
            $disabled = '';
            if (!$beVariant->getIsAvailable() && $beVariant->getProduct()->getHandleStockInVariants()) {
                $disabled = 'disabled';
            }

            $option = '<option ' . $value . ' ' . $data . ' ' . $disabled . '>' . $optionLabel . '</option>';
            $options[$optionLabel] = $option;
        }

        return $options;
    }

    /**
     * @param \Extcode\Cart\Domain\Model\Product\BeVariant $beVariant
     *
     * @return string
     */
    protected function getOptionLabel(
        \Extcode\Cart\Domain\Model\Product\BeVariant $beVariant
    ) {
        $optionLabelArray = [];

        if ($this->product->getBeVariantAttribute1()) {
            $optionLabelArray[] = $beVariant->getBeVariantAttributeOption1()->getTitle();
        }
        if ($this->product->getBeVariantAttribute2()) {
            $optionLabelArray[] = $beVariant->getBeVariantAttributeOption2()->getTitle();
        }
        if ($this->product->getBeVariantAttribute3()) {
            $optionLabelArray[] = $beVariant->getBeVariantAttributeOption3()->getTitle();
        }
        $optionLabel = implode(' - ', $optionLabelArray);

        return $optionLabel;
    }
}
