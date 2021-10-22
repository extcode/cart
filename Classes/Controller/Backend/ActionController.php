<?php

namespace Extcode\Cart\Controller\Backend;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Utility\OrderUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;

class ActionController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var OrderUtility
     */
    protected $orderUtility;

    /**
     * @var array
     */
    protected $pluginSettings = [];

    public function injectOrderUtility(OrderUtility $orderUtility): void
    {
        $this->orderUtility = $orderUtility;
    }

    protected function initializeAction(): void
    {
        $this->pluginSettings =
            $this->configurationManager->getConfiguration(
                ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
            );

        $pageId = (int)(GeneralUtility::_GET('id')) ? GeneralUtility::_GET('id') : 1;

        BackendUtility::readPageAccess(
            $pageId,
            $GLOBALS['BE_USER']->getPagePermsClause(1)
        );

        $configurationManager = GeneralUtility::makeInstance(
            ConfigurationManager::class
        );

        $frameworkConf =
            $configurationManager->getConfiguration(
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
