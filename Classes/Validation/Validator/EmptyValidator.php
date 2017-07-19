<?php
namespace Extcode\Cart\Validation\Validator;

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
 * Cart Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class EmptyValidator extends \TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator
{
    /**
     * This validator always needs to be executed even if the given value is empty.
     * See AbstractValidator::validate()
     *
     * @var bool
     */
    protected $acceptsEmptyValues = false;

    /**
     * Checks if the given property ($propertyValue) is not empty (NULL, empty string, empty array or empty object).
     *
     * @param mixed $value The value that should be validated
     */
    public function isValid($value)
    {
        if ($value != null) {
            if (is_numeric($value)) {
                $this->addError(
                    $this->translateErrorMessage(
                        'validator.empty.notempty',
                        'cart'
                    ),
                    1500493634
                );
            } elseif (is_string($value) && $value != '') {
                $this->addError(
                    $this->translateErrorMessage(
                        'validator.empty.notempty',
                        'cart'
                    ),
                    1500493641
                );
            } elseif (is_array($value) && !empty($value)) {
                $this->addError(
                    $this->translateErrorMessage(
                        'validator.empty.notempty',
                        'cart'
                    ),
                    1500493650
                );
            } elseif (is_object($value) && $value instanceof \Countable && $value->count() != 0) {
                $this->addError(
                    $this->translateErrorMessage(
                        'validator.empty.notempty',
                        'cart'
                    ),
                    1500493656
                );
            }
        }
    }
}
