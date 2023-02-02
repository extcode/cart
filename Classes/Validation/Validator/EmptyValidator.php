<?php

namespace Extcode\Cart\Validation\Validator;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;

class EmptyValidator extends AbstractValidator
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
    public function isValid(mixed $value): void
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
            } elseif (
                is_string($value) &&
                $value != ''
            ) {
                $this->addError(
                    $this->translateErrorMessage(
                        'validator.empty.notempty',
                        'cart'
                    ),
                    1500493641
                );
            } elseif (
                is_array($value) &&
                !empty($value)
            ) {
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
