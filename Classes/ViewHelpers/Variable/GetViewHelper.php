<?php

namespace Extcode\Cart\ViewHelpers\Variable;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * ### Variable: Get
 *
 * ViewHelper used to read the value of a current template
 * variable. Can be used with dynamic indices in arrays:
 *
 *     <v:variable.get name="array.{dynamicIndex}" />
 *     <v:variable.get name="array.{v:variable.get(name: 'arrayOfSelectedKeys.{indexInArray}')}" />
 *     <f:for each="{v:variable.get(name: 'object.arrayProperty.{dynamicIndex}')}" as="nestedObject">
 *         ...
 *     </f:for>
 *
 * Or to read names of variables which contain dynamic parts:
 *
 *     <!-- if {variableName} is "Name", outputs value of {dynamicName} -->
 *     {v:variable.get(name: 'dynamic{variableName}')}
 *
 * If your target object is an array with unsequential yet
 * numeric indices (e.g. {123: 'value1', 513: 'value2'},
 * commonly seen in reindexed UID map arrays) use
 * `useRawIndex="TRUE"` to indicate you do not want your
 * array/QueryResult/Iterator to be accessed by locating
 * the Nth element - which is the default behavior.
 *
 * ```warning
 * Do not try `useRawKeys="TRUE"` on QueryResult or
 * ObjectStorage unless you are fully aware what you are
 * doing. These particular types require an unpredictable
 * index value - the SPL object hash value - when accessing
 * members directly. This SPL indexing and the very common
 * occurrences of QueryResult and ObjectStorage variables
 * in templates is the very reason why `useRawKeys` by
 * default is set to `FALSE`.
 * ```
 */
class GetViewHelper extends AbstractViewHelper
{
    public function initializeArguments()
    {
        parent::initializeArguments();

        $this->registerArgument(
            'name',
            'string',
            'Name of variable to get.',
            true
        );
        $this->registerArgument(
            'useRawKeys',
            'bool',
            'Value of variable to set.',
            false,
            false
        );
    }

    /**
     * Get the value of $name.
     *
     * @return mixed
     */
    public function render()
    {
        $name = $this->arguments['name'];
        $useRawKeys = $this->arguments['useRawKeys'];

        if (!str_contains($name, '.')) {
            if ($this->templateVariableContainer->exists($name) === true) {
                return $this->templateVariableContainer->get($name);
            }
        } else {
            $segments = explode('.', $name);
            $templateVariableRootName = $lastSegment = array_shift($segments);
            if ($this->templateVariableContainer->exists($templateVariableRootName) === true) {
                $templateVariableRoot = $this->templateVariableContainer->get($templateVariableRootName);
                if ($useRawKeys === true) {
                    return ObjectAccess::getPropertyPath($templateVariableRoot, implode('.', $segments));
                }
                try {
                    $value = $templateVariableRoot;
                    foreach ($segments as $segment) {
                        if (ctype_digit($segment) === true) {
                            $segment = (int)$segment;
                            $index = 0;
                            // Note: this loop approach is not a stupid solution. If you doubt this,
                            // attempt to feth a number at a numeric index from ObjectStorage ;)
                            foreach ($value as $possibleValue) {
                                if ($index === $segment) {
                                    $value = $possibleValue;
                                    break;
                                }
                                ++ $index;
                            }
                            continue;
                        }
                        $value = ObjectAccess::getProperty($value, $segment);
                    }
                    return $value;
                } catch (\Exception $e) {
                    return null;
                }
            }
        }
        return null;
    }
}
