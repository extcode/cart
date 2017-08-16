<?php

namespace Extcode\Cart\Hooks;

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
use DmitryDulepov\Realurl\Configuration\ConfigurationReader;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * RealUrlHook
 *
 * @author Daniel Lorenz <ext.cart@extco.de>
 */
class RealUrlHook
{
    /**
     * @param array &$parameters
     * @param ConfigurationReader $configurationReader
     */
    public function postProcessConfiguration(&$parameters, ConfigurationReader $configurationReader)
    {
        if (!isset($parameters['configuration']['fixedPostVars']['cartShowProduct']) ||
            !isset($parameters['configuration']['fixedPostVars']['cartShowCart'])
        ) {
            return;
        }

        if ($configurationReader->getMode() === ConfigurationReader::MODE_DECODE) {
            $targetPageId = $this->getTypoScriptFrontendController()->id;
            $pageRecord = $this->getTypoScriptFrontendController()->page;
        } else {
            $targetPageId = $parameters['urlParameters']['id'];
            $pageRepository = GeneralUtility::makeInstance('TYPO3\\CMS\\Frontend\\Page\\PageRepository');
            $pageRecord = $pageRepository->getPage($parameters['urlParameters']['id']);
        }

        if ($pageRecord) {
            switch ((int)$pageRecord['doktype']) {
                case 181:
                    $parameters['configuration']['fixedPostVars'][$targetPageId] = 'cartShowCart';
                    break;
                case 182:
                    $parameters['configuration']['fixedPostVars'][$targetPageId] = 'cartShowProduct';
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
