<?php

namespace Extcode\Cart\Controller\Backend;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use Extcode\Cart\Domain\Model\Order\Item;
use Extcode\Cart\Utility\OrderUtility;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Site\SiteFinder;
use TYPO3\CMS\Core\TimeTracker\TimeTracker;
use TYPO3\CMS\Core\TypoScript\TypoScriptService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManager;
use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

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

        $this->pageinfo = BackendUtility::readPageAccess(
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

    protected function generateNumber(Item $item, string $pdfType): string
    {
        $this->buildTSFE((int)GeneralUtility::_GP('id'));

        $typoScriptService = GeneralUtility::makeInstance(
            TypoScriptService::class
        );

        $configurationManager = GeneralUtility::makeInstance(ConfigurationManager::class);

        $cartConfiguration = $configurationManager->getConfiguration(
            ConfigurationManager::CONFIGURATION_TYPE_FRAMEWORK
        );

        if ($cartConfiguration) {
            $typoScriptSettings = $typoScriptService->convertTypoScriptArrayToPlainArray($cartConfiguration);
        }

        //TODO replace it width dynamic var
        $typoScriptSettings['settings'] = [
            'cart' => [
                'pid' => $item->getCartPid(),
            ],
        ];

        return $this->orderUtility->getNumber($typoScriptSettings, $pdfType);
    }

    protected function buildTSFE(int $pid = 1): void
    {
        if (!is_object($GLOBALS['TT'])) {
            $GLOBALS['TT'] = new TimeTracker(false);
            GeneralUtility::makeInstance(TimeTracker::class)->start();
        }

        $siteFinder = GeneralUtility::makeInstance(SiteFinder::class);
        $siteLanguages = $siteFinder->getSiteByPageId($pid)->getLanguages();

        $GLOBALS['TSFE'] = GeneralUtility::makeInstance(
            TypoScriptFrontendController::class,
            $GLOBALS['TYPO3_CONF_VARS'],
            $pid,
            $siteLanguages[0]
        );
    }
}
