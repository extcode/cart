<?php

declare(strict_types=1);

namespace Extcode\Cart\Controller\Backend;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;

class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    protected array $pluginSettings = [];
    public function __construct(
        protected ConfigurationManagerInterface $configurationManager
    ) {}

    protected function initializeAction(): void
    {
        $this->pluginSettings
            = $this->configurationManager->getConfiguration(
                ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
            );

        $pageId = (int)($this->request->getQueryParams()['id'] ?? 1);

        BackendUtility::readPageAccess(
            $pageId,
            $GLOBALS['BE_USER']->getPagePermsClause(1)
        );

        $configurationManager = $this->configurationManager;

        $frameworkConf
            = $configurationManager->getConfiguration(
                ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
            );
        $persistenceConf = ['persistence' => ['storagePid' => $pageId]];
        $configurationManager->setConfiguration(array_merge($frameworkConf, $persistenceConf));

        $this->settings = $configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_SETTINGS,
            $this->request->getControllerExtensionName(),
            $this->request->getPluginName()
        );
    }
}
