<?php

namespace Extcode\Cart\Controller\Backend;

/**
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Action Controller
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
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
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );

        $pageId = (int)(GeneralUtility::_GET('id')) ? GeneralUtility::_GET('id') : 1;

        $this->pageinfo = \TYPO3\CMS\Backend\Utility\BackendUtility::readPageAccess(
            $pageId,
            $GLOBALS['BE_USER']->getPagePermsClause(1)
        );

        $configurationManager = $this->objectManager->get(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::class
        );

        $frameworkConf =
            $configurationManager->getConfiguration(
                \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FRAMEWORK
            );
        $persistenceConf = ['persistence' => ['storagePid' => $pageId]];
        $configurationManager->setConfiguration(array_merge($frameworkConf, $persistenceConf));

        $this->settings = $configurationManager->getConfiguration(
            \TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_SETTINGS,
            $this->request->getControllerExtensionName(),
            $this->request->getPluginName()
        );
    }
}
