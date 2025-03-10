<?php

namespace Extcode\Cart\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManagerInterface as ExtFormConfigurationManagerInterface;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManagerInterface;

class FormDefinitions
{
    public function __construct(
        private readonly FormPersistenceManagerInterface $formPersistenceManager,
        private readonly ConfigurationManagerInterface $configurationManager,
        private readonly ExtFormConfigurationManagerInterface $extFormConfigurationManager,
    ) {}

    public function getItems(array &$config): void
    {
        if ($config['field'] !== 'form_definition') {
            return;
        }

        $prototypeName = 'cart';
        if (is_array($config['config']['itemsProcFuncConfig']) && !empty($config['config']['itemsProcFuncConfig']['prototypeName'])) {
            $prototypeName = $config['config']['itemsProcFuncConfig']['prototypeName'];
        }

        $formSettings = $this->getFormSettings();
        $availableForms = $this->formPersistenceManager->listForms($formSettings);

        foreach ($availableForms as $availableForm) {
            $form = $this->formPersistenceManager->load($availableForm['persistenceIdentifier'], $formSettings, []);

            if ($form['prototypeName'] === $prototypeName) {
                $config['items'][] = [
                    0 => $availableForm['name'],
                    1 => $availableForm['persistenceIdentifier'],
                ];
            }
        }
    }

    private function getFormSettings(): array
    {
        $typoScriptSettings = $this->configurationManager->getConfiguration(ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS, 'form');
        $formSettings = $this->extFormConfigurationManager->getYamlConfiguration($typoScriptSettings, false);
        if (!isset($formSettings['formManager'])) {
            // Config sub array formManager is crucial and should always exist. If it does
            // not, this indicates an issue in config loading logic. Except in this case.
            throw new \LogicException('Configuration could not be loaded', 1723717461);
        }
        return $formSettings;
    }
}
