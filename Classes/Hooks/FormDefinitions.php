<?php

declare(strict_types=1);

namespace Extcode\Cart\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use LogicException;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Form\Domain\DTO\FormMetadata;
use TYPO3\CMS\Form\Domain\DTO\SearchCriteria;
use TYPO3\CMS\Form\Exception as FormException;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManagerInterface as ExtFormConfigurationManagerInterface;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManagerInterface;

class FormDefinitions
{
    /**
     * Type identifier of TYPO3\CMS\Form\Storage\DatabaseStorageAdapter
     */
    private const DATABASE_STORAGE_TYPE = 'database';

    public function __construct(
        private readonly FormPersistenceManagerInterface $formPersistenceManager,
        private readonly ConfigurationManagerInterface $configurationManager,
        private readonly ExtFormConfigurationManagerInterface $extFormConfigurationManager,
    ) {
    }

    public function getItems(array &$config): void
    {
        if ($config['field'] !== 'form_definition') {
            return;
        }

        $prototypeName = 'cart';
        if (is_array($config['config']['itemsProcFuncConfig']) && !empty($config['config']['itemsProcFuncConfig']['prototypeName'])) {
            $prototypeName = $config['config']['itemsProcFuncConfig']['prototypeName'];
        }

        $availableForms = $this->formPersistenceManager->listForms($this->getFormSettings(), new SearchCriteria());

        foreach ($availableForms as $availableForm) {
            if (!$availableForm instanceof FormMetadata
                || $availableForm->invalid
                || $this->getPrototypeName($availableForm) !== $prototypeName
            ) {
                continue;
            }

            $config['items'][] = [
                'label' => $availableForm->name,
                'value' => $availableForm->persistenceIdentifier,
            ];
        }
    }

    /**
     * The file storages read the prototype name from the form definition for
     * the listing. The database storage does not, it always lists the forms
     * with the prototype "standard". Only these forms are loaded completely.
     */
    private function getPrototypeName(FormMetadata $formMetadata): ?string
    {
        if ($formMetadata->storageType !== self::DATABASE_STORAGE_TYPE) {
            return $formMetadata->prototypeName;
        }
        if ($formMetadata->persistenceIdentifier === null) {
            return null;
        }

        try {
            $formDefinition = $this->formPersistenceManager->load($formMetadata->persistenceIdentifier);
        } catch (FormException) {
            return null;
        }

        $prototypeName = $formDefinition['prototypeName'] ?? null;

        return is_string($prototypeName) ? $prototypeName : null;
    }

    private function getFormSettings(): array
    {
        $typoScriptSettings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'form');
        $formSettings = $this->extFormConfigurationManager->getYamlConfiguration($typoScriptSettings, false);
        if (!isset($formSettings['formManager'])) {
            // Config sub array formManager is crucial and should always exist. If it does
            // not, this indicates an issue in config loading logic. Except in this case.
            throw new LogicException('Configuration could not be loaded', 1723717461);
        }

        return $formSettings;
    }
}
