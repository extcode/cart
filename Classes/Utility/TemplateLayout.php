<?php

namespace Extcode\Cart\Utility;

/*
 * This file is part of the package extcode/cart.
 *
 * For the full copyright and license information, please read the
 * LICENSE file that was distributed with this source code.
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class TemplateLayout implements SingletonInterface
{
    /**
     * Get available template layouts for a certain page
     *
     * @param int $pageUid
     * @param string $extKey
     * @param string $pluginName
     * @return array
     */
    public function getAvailableTemplateLayouts($pageUid, $extKey, $pluginName)
    {
        $templateLayouts = [];
        $pluginName = GeneralUtility::camelCaseToLowerCaseUnderscored($pluginName);

        if (!empty($pluginName)) {
            if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT'][$extKey]['templateLayouts'][$pluginName])
                && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT'][$extKey]['templateLayouts'][$pluginName])
            ) {
                $templateLayouts = $GLOBALS['TYPO3_CONF_VARS']['EXT'][$extKey]['templateLayouts'][$pluginName];
            }
        } else {
            if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT'][$extKey]['templateLayouts'])
                && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT'][$extKey]['templateLayouts'])
            ) {
                $templateLayouts = $GLOBALS['TYPO3_CONF_VARS']['EXT'][$extKey]['templateLayouts'];
            }
        }

        // Add TsConfig values
        foreach ($this->getTemplateLayoutsFromTsConfig($pageUid, $extKey, $pluginName) as $templateKey => $title) {
            if (\str_starts_with($title, '--div--')) {
                $optGroupParts = GeneralUtility::trimExplode(',', $title, true, 2);
                $title = $optGroupParts[1];
                $templateKey = $optGroupParts[0];
            }
            $templateLayouts[] = [$title, $templateKey];
        }

        return $templateLayouts;
    }

    /**
     * Get template layouts defined in TsConfig
     *
     * @param $pageUid
     * @param string $pluginName
     * @return array
     */
    protected function getTemplateLayoutsFromTsConfig($pageUid, $extKey, $pluginName)
    {
        $templateLayouts = [];
        $pagesTsConfig = BackendUtility::getPagesTSconfig($pageUid);
        $extKey = 'tx_' . preg_replace('/_/', '', $extKey) . '.';

        if (!empty($pluginName)) {
            $pluginName .= '.';
            if (isset($pagesTsConfig[$extKey]['templateLayouts.']) &&
                isset($pagesTsConfig[$extKey]['templateLayouts.'][$pluginName]) &&
                is_array($pagesTsConfig[$extKey]['templateLayouts.'][$pluginName])
            ) {
                $templateLayouts = $pagesTsConfig[$extKey]['templateLayouts.'][$pluginName];
            }
        } else {
            if (isset($pagesTsConfig[$extKey]['templateLayouts.']) &&
                is_array($pagesTsConfig[$extKey]['templateLayouts.'])
            ) {
                $templateLayouts = $pagesTsConfig[$extKey]['templateLayouts.'];
            }
        }
        return $templateLayouts;
    }
}
