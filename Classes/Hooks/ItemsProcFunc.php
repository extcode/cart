<?php

namespace Extcode\Cart\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */
use Extcode\Cart\Utility\TemplateLayout;
use TYPO3\CMS\Backend\Utility\BackendUtility as BackendUtilityCore;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManagerInterface as ExtFormConfigurationManagerInterface;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManagerInterface;

class ItemsProcFunc
{
    protected TemplateLayout $templateLayoutsUtility;

    public function __construct(
        private readonly FormPersistenceManagerInterface $formPersistenceManager,
        private readonly ConfigurationManagerInterface $configurationManager,
        private readonly ExtFormConfigurationManagerInterface $extFormConfigurationManager,
    ) {
        $this->templateLayoutsUtility = GeneralUtility::makeInstance(TemplateLayout::class);
    }

    /**
     * Itemsproc function to extend the selection of templateLayouts in the plugin
     */
    public function user_templateLayout(array &$config): void
    {
        $pageId = 0;
        $currentColPos = $config['flexParentDatabaseRow']['colPos'];
        $pageId = $this->getPageId($config['flexParentDatabaseRow']['pid']);

        $extKey = $config['config']['extKey'];
        $pluginName = $config['config']['pluginName'];

        if ($pageId > 0) {
            $templateLayouts = $this->templateLayoutsUtility->getAvailableTemplateLayouts($pageId, $extKey, $pluginName);

            $templateLayouts = $this->reduceTemplateLayouts($templateLayouts, $currentColPos);
            foreach ($templateLayouts as $layout) {
                $additionalLayout = [
                    htmlspecialchars($this->getLanguageService()->sL($layout[0])),
                    $layout[1],
                ];
                array_push($config['items'], $additionalLayout);
            }
        }
    }

    public function user_formDefinition(array &$config): void
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

    /**
     * Reduce the template layouts by the ones that are not allowed in given colPos
     */
    protected function reduceTemplateLayouts(array $templateLayouts, int $currentColPos): array
    {
        $currentColPos = (int)$currentColPos;
        $restrictions = [];
        $allLayouts = [];
        foreach ($templateLayouts as $key => $layout) {
            if (is_array($layout[0])) {
                if (isset($layout[0]['allowedColPos']) && \str_ends_with((string)$layout[1], '.')) {
                    $layoutKey = substr((string)$layout[1], 0, -1);
                    $restrictions[$layoutKey] = GeneralUtility::intExplode(',', $layout[0]['allowedColPos'], true);
                }
            } else {
                $allLayouts[$key] = $layout;
            }
        }
        if (!empty($restrictions)) {
            foreach ($restrictions as $restrictedIdentifier => $restrictedColPosList) {
                if (!in_array($currentColPos, $restrictedColPosList, true)) {
                    unset($allLayouts[$restrictedIdentifier]);
                }
            }
        }

        return $allLayouts;
    }

    /**
     * Get page id, if negative, then it is a "after record"
     */
    protected function getPageId(int $pid): int
    {
        $pid = (int)$pid;

        if ($pid > 0) {
            return $pid;
        }

        $row = BackendUtilityCore::getRecord('tt_content', abs($pid), 'uid,pid');
        return $row['pid'];
    }

    protected function getLanguageService(): LanguageService
    {
        return $GLOBALS['LANG'];
    }
}
