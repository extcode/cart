<?php

namespace Extcode\Cart\Controller\Backend;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * Plugin Settings
     *
     * @var array
     */
    protected $pluginSettings;

    /**
     * Initialize Action
     */
    protected function initializeAction()
    {
        $this->pluginSettings =
            $this->configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
            );

        $pageId = (int)(GeneralUtility::_GET('id')) ? GeneralUtility::_GET('id') : 1;

        $this->pageinfo = \TYPO3\CMS\Backend\Utility\BackendUtility::readPageAccess(
            $pageId,
            $GLOBALS['BE_USER']->getPagePermsClause(1)
        );

        $configurationManager = GeneralUtility::makeInstance(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class
        );

        $frameworkConf =
            $configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
            );
        $persistenceConf = ['persistence' => ['storagePid' => $pageId]];
        $configurationManager->setConfiguration(array_merge($frameworkConf, $persistenceConf));

        $this->settings = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManager::CONFIGURATION_TYPE_SETTINGS,
            $this->request->getControllerExtensionName(),
            $this->request->getPluginName()
        );
    }
}
