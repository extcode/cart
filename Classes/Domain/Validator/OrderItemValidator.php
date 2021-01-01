<?php
namespace Extcode\Cart\Domain\Validator;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item as OrderItem;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Validation\Validator\AbstractValidator;
use TYPO3\CMS\Extbase\Validation\Validator\ObjectValidatorInterface;
use TYPO3\CMS\Extbase\Validation\Validator\ValidatorInterface;

/**
 * A generic object validator which allows for specifying property validators
 */
class OrderItemValidator extends AbstractValidator implements ObjectValidatorInterface
{
    /**
     * @var \SplObjectStorage[]
     */
    protected $propertyValidators = [];

    /**
     * Checks if the given value is valid according to the validator, and returns
     * the Error Messages object which occurred.
     *
     * @param mixed $value The value that should be validated
     * @return Result
     * @api
     */
    public function validate($value)
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
     *
     * In case the object is a doctrine proxy, we need to load the real instance first.
     *
     * @param OrderItem $item
     * @param string $propertyName
     * @return mixed
     */
    protected function getPropertyValue(OrderItem $item, $propertyName)
    {
        $methodPrefixes = ['get', 'is'];

        foreach ($methodPrefixes as $methodPrefix) {
            $getter = $methodPrefix . ucfirst($propertyName);
            if (method_exists($item, $getter)) {
                return $item->$getter();
            }
        }

        return null;
    }

    /**
     * Checks if the specified property of the given object is valid, and adds
     * found errors to the $messages object.
     *
     * @param mixed $value The value to be validated
     * @param \Traversable $validators The validators to be called on the value
     * @param string $propertyName Name of ther property to check
     */
    protected function checkProperty($value, $validators, $propertyName)
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
     * Checks if the given value is valid according to the property validators.
     *
     * @param mixed $object The value that should be validated
     * @api
     */
    protected function isValid($object)
    {
        foreach ($this->propertyValidators as $propertyName => $validators) {
            $propertyValue = $this->getPropertyValue($object, $propertyName);
            $this->checkProperty($propertyValue, $validators, $propertyName);
        }
    }

    /**
     * Checks if the specified property of the given object is valid.
     *
     * If at least one error occurred, the result is FALSE.
     *
     * @param object $object The object containing the property to validate
     * @param string $propertyName Name of the property to validate
     * @return bool TRUE if the property value is valid, FALSE if an error occurred
     *
     * @deprecated since Extbase 1.4.0, will be removed two versions after Extbase 6.1
     * @api
     */
    public function isPropertyValid($object, $propertyName)
    {
        return false;
    }

    /**
     * Checks the given object can be validated by the validator implementation
     *
     * @param mixed $object The object to be checked
     * @return bool TRUE if the given value can be validated
     * @api
     */
    public function canValidate($object)
    {
        if (
            is_object($object)
            && $object instanceof \TYPO3\CMS\Form\Domain\Model\ValidationElement
        ) {
            return true;
        }
        return false;
    }

    /**
     * Adds the given validator for validation of the specified property.
     *
     * @param string $propertyName Name of the property to validate
     * @param ValidatorInterface $validator The property validator
     * @api
     */
    public function addPropertyValidator($propertyName, ValidatorInterface $validator)
    {
        if (!isset($this->propertyValidators[$propertyName])) {
            $this->propertyValidators[$propertyName] = new \SplObjectStorage();
        }
        $this->propertyValidators[$propertyName]->attach($validator);
    }

    /**
     * @param object $object
     * @return bool
     */
    protected function isValidatedAlready($object)
    {
        if ($this->validatedInstancesContainer === null) {
            $this->validatedInstancesContainer = new \SplObjectStorage();
        }
        if ($this->validatedInstancesContainer->contains($object)) {
            return true;
        } else {
            $this->validatedInstancesContainer->attach($object);

            return false;
        }
    }

    /**
     * Returns all property validators - or only validators of the specified property
     *
     * @param string $propertyName Name of the property to return validators for
     * @return array An array of validators
     */
    public function getPropertyValidators($propertyName = null)
    {
        if ($propertyName !== null) {
            return (isset($this->propertyValidators[$propertyName])) ? $this->propertyValidators[$propertyName] : [];
        } else {
            return $this->propertyValidators;
        }
    }

    /**
     * @return int
     */
    public function countPropertyValidators()
    {
        $count = 0;
        foreach ($this->propertyValidators as $propertyValidators) {
            $count += $propertyValidators->count();
        }
        return $count;
    }

    /**
     * @var \SplObjectStorage
     */
    protected $validatedInstancesContainer;

    /**
     * Allows to set a container to keep track of validated instances.
     *
     * @param \SplObjectStorage $validatedInstancesContainer A container to keep track of validated instances
     * @api
     */
    public function setValidatedInstancesContainer(\SplObjectStorage $validatedInstancesContainer)
    {
        $this->validatedInstancesContainer = $validatedInstancesContainer;
    }
}
