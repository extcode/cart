<?php

declare(strict_types=1);

namespace Extcode\Cart\ViewHelpers;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Reflection\ObjectAccess;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

class MapModelPropertiesToTableColumnsViewHelper extends AbstractViewHelper
{
    /**
     * @var array<mixed>
     */
    protected array $configuration;

    public function __construct(
        protected readonly ConfigurationManager $configurationManager
    ) {
        $this->configuration = $this->configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
        );
    }

    public function initializeArguments(): void
    {
        parent::initializeArguments();

        $this->registerArgument(
            'class',
            'string',
            'class',
            true
        );
        $this->registerArgument(
            'table',
            'string',
            'table',
            true
        );
        $this->registerArgument(
            'data',
            'string',
            'data',
            false
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function render(): array
    {
        $class = $this->arguments['class'];
        $table = $this->arguments['table'];
        $data = $this->arguments['data'];

        if (isset($this->configuration['persistence']['classes'][$class]['mapping'])
            && $this->configuration['persistence']['classes'][$class]['mapping']['tableName'] == $table
        ) {
            $mapping = [];
            foreach ($this->configuration['persistence']['classes'][$class]['mapping']['columns'] as $tableColumn => $modelPropertyData) {
                $modelProperty = $modelPropertyData['mapOnProperty'];
                if (is_string($modelProperty) === false) {
                    continue;
                }
                $mapping[$modelProperty] = $tableColumn;
            }

            $data = ObjectAccess::getGettableProperties($data);

            foreach ($data as $key => $value) {
                if (isset($mapping[$key]) && is_string($mapping[$key])) {
                    unset($data[$key]);
                    $data[(string) $mapping[$key]] = $value;
                }
            }

            return $data;
        }

        return $data;
    }
}
