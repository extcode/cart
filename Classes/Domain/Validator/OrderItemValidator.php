<?php

namespace Extcode\Cart\Domain\Validator;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractGenericObjectValidator;
use TYPO3\CMS\Extbase\Validation\Validator\ObjectValidatorInterface;
use TYPO3\CMS\Extbase\Validation\Validator\ValidatorInterface;

/**
 * A generic object validator which allows for specifying property validators
 */
class OrderItemValidator extends AbstractGenericObjectValidator
{
    /**
     * Checks if the given value is valid according to the validator, and returns
     * the Error Messages object which occurred.
     *
     * @param mixed $value The value that should be validated
     */
    public function validate(mixed $value): Result
    {
        $this->result = new Result();
        if ($this->acceptsEmptyValues === false || $this->isEmpty($value) === false) {
            if (!is_object($value)) {
                $this->addError('Object expected, %1$s given.', 1241099149, [gettype($value)]);
            } elseif ($this->isValidatedAlready($value) === false) {
                $this->isValid($value);
            }
        }

        return $this->result;
    }

    /**
     * Load the property value to be used for validation.
     * In case the object is a doctrine proxy, we need to load the real instance first.
     */
    protected function getPropertyValue(object $object, string $propertyName): mixed
    {
        $methodPrefixes = ['get', 'is'];

        foreach ($methodPrefixes as $methodPrefix) {
            $getter = $methodPrefix . ucfirst($propertyName);
            if (method_exists($object, $getter)) {
                return $object->$getter();
            }
        }

        return null;
    }

    /**
     * Checks if the specified property of the given object is valid, and adds
     * found errors to the $messages object.
     */
    protected function checkProperty(mixed $value, \Traversable $validators, string $propertyName): void
    {
        /** @var Result $result */
        $result = null;
        foreach ($validators as $validator) {
            if ($validator instanceof ObjectValidatorInterface) {
                $validator->setValidatedInstancesContainer($this->validatedInstancesContainer);
            }

            /**
             * File upload validation.
             *
             * If a $_FILES array is found in the request data,
             * iterate over all requested files and validate each
             * single file.
             */
            if (
                isset($value[0]['name'])
                && isset($value[0]['type'])
                && isset($value[0]['tmp_name'])
                && isset($value[0]['size'])
            ) {
                foreach ($value as $file) {
                    $currentResult = $validator->validate($file);
                    if ($currentResult->hasMessages()) {
                        if ($result == null) {
                            $result = $currentResult;
                        } else {
                            $result->merge($currentResult);
                        }
                    }
                }
            } else {
                $currentResult = $validator->validate($value);
                if ($currentResult->hasMessages()) {
                    if ($result == null) {
                        $result = $currentResult;
                    } else {
                        $result->merge($currentResult);
                    }
                }
            }
        }
        if ($result != null) {
            $this->result->forProperty($propertyName)->merge($result);
        }
    }

    /**
     * Check if $value is valid. If it is not valid, needs to add an error to result.
     */
    protected function isValid(mixed $object): void
    {
        foreach ($this->propertyValidators as $propertyName => $validators) {
            $propertyValue = $this->getPropertyValue($object, $propertyName);
            $this->checkProperty($propertyValue, $validators, $propertyName);
        }
    }

    /**
     * Adds the given validator for validation of the specified property.
     */
    public function addPropertyValidator(string $propertyName, ValidatorInterface $validator): void
    {
        if (!isset($this->propertyValidators[$propertyName])) {
            $this->propertyValidators[$propertyName] = new \SplObjectStorage();
        }
        $this->propertyValidators[$propertyName]->attach($validator);
    }

    protected function isValidatedAlready(object $object): bool
    {
        if ($this->validatedInstancesContainer === null) {
            $this->validatedInstancesContainer = new \SplObjectStorage();
        }
        if ($this->validatedInstancesContainer->contains($object)) {
            return true;
        }

        $this->validatedInstancesContainer->attach($object);

        return false;
    }

    public function countPropertyValidators(): int
    {
        $count = 0;
        foreach ($this->propertyValidators as $propertyValidators) {
            $count += $propertyValidators->count();
        }
        return $count;
    }
}
