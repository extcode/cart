<?php

namespace Extcode\Cart\Hooks;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use DmitryDulepov\Realurl\Configuration\ConfigurationReader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class RealUrlHook
{
    /**
     * @param array &$parameters
     * @param ConfigurationReader $configurationReader
     */
    public function postProcessConfiguration(&$parameters, ConfigurationReader $configurationReader)
    {
        if (!isset($parameters['configuration']['fixedPostVars']['cartShowCart'])
        ) {
            return;
        }

        if ($configurationReader->getMode() === ConfigurationReader::MODE_DECODE) {
            $targetPageId = $this->getTypoScriptFrontendController()->id;
            $pageRecord = $this->getTypoScriptFrontendController()->page;
        } else {
            $targetPageId = $parameters['urlParameters']['id'];
            $pageRepository = GeneralUtility::makeInstance(
                \TYPO3\CMS\Frontend\Page\PageRepository::class
            );
            $pageRecord = $pageRepository->getPage($parameters['urlParameters']['id']);
        }

        if ($pageRecord) {
            switch ((int)$pageRecord['doktype']) {
                case 181:
                    $parameters['configuration']['fixedPostVars'][$targetPageId] = 'cartShowCart';
                    break;
            }
        }

        return;
    }

    /**
     * @return \TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController
     */
    protected function getTypoScriptFrontendController()
    {
        return $GLOBALS['TSFE'];
    }
}
