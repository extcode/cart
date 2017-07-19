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
 * Select ViewHelper
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class SelectViewHelper extends \TYPO3\CMS\Fluid\ViewHelpers\Form\SelectViewHelper
{
    /**
     * Initialize arguments.
     *
     * @api
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument(
            'translationKey',
            'string',
            'If specified, will call the label in locallang file.'
        );
        $this->registerArgument(
            'required',
            'bool',
            'If the field is required or not',
            false,
            false
        );
    }

    /**
     * Render the tag.
     *
     * @return string rendered tag.
     * @api
     */
    public function render()
    {
        if ($this->arguments['required']) {
            $this->tag->addAttribute('required', 'required');
        }

        return parent::render();
    }

    /**
     * Render one option tag
     *
     * @param string $value value attribute of the option tag (will be escaped)
     * @param string $label content of the option tag (will be escaped)
     * @param bool $isSelected specifies wheter or not to add selected attribute
     *
     * @return string the rendered option tag
     */
    protected function renderOptionTag($value, $label, $isSelected)
    {
        if ($value == 'default') {
            return '';
        }
        $output = '<option value="' . htmlspecialchars($value) . '"';
        if ($isSelected) {
            $output .= ' selected="selected"';
        }
        $output .= '>';
        $output .= $this->translateOptionValue($value, $label);
        $output .= '</option>';
        return $output;
    }

    /**
     * Translate the option value if translationKey is given
     *
     * @param string $value value attribute of the option tag (will be escaped)
     * @param string $label content of the option tag (will be escaped)
     *
     * @return string the rendered option tag
     */
    protected function translateOptionValue($value, $label)
    {
        $key = $this->arguments['translationKey'];

        if ($key != null) {
            $output = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate(
                $key . '.' . htmlspecialchars($value),
                'Cart'
            );
            if ($output === null) {
                $output = htmlspecialchars($label);
            }
        } else {
            $output = htmlspecialchars($label);
        }

        return $output;
    }
}
