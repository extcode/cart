<?php

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

class MapModelPropertiesToTableColumnsViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper
{
    /**
     * render
     *
     * @param string $class
     * @param string $table
     * @param object $data
     * @return array
     */
    public function render($class = '', $table = '', $data)
    {
        $configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
        $conf = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
        );

        if (isset($conf['persistence']['classes'][$class]['mapping']) &&
            $conf['persistence']['classes'][$class]['mapping']['tableName'] == $table
        ) {
            $mapping = [];
            foreach ($conf['persistence']['classes'][$class]['mapping']['columns'] as $tableColumn => $modelPropertyData) {
                $modelProperty = $modelPropertyData['mapOnProperty'];
                $mapping[$modelProperty] = $tableColumn;
            }

            $data = \TYPO3\CMS\Extbase\Reflection\ObjectAccess::getGettableProperties($data);

            foreach ($data as $key => $value) {
                if (isset($mapping[$key])) {
                    unset($data[$key]);
                    $data[$mapping[$key]] = $value;
                }
            }

            return $data;
        } else {
            return $data;
        }
    }
}
