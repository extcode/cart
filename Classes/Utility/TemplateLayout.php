<?php

namespace Extcode\Cart\Utility;

/**
 * This file is part of the "cart" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\SingletonInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * TemplateLayout utility class
 */
class TemplateLayout implements SingletonInterface
{

    /**
     * Get available template layouts for a certain page
     *
     * @param int $pageUid
     * @param string $extKey
     * @return array
     */
    public function getAvailableTemplateLayouts($pageUid, $extKey)
    {
        $templateLayouts = [];

        // Check if the layouts are extended by ext_tables
        if (isset($GLOBALS['TYPO3_CONF_VARS']['EXT'][$extKey]['templateLayouts'])
            && is_array($GLOBALS['TYPO3_CONF_VARS']['EXT'][$extKey]['templateLayouts'])
        ) {
            $templateLayouts = $GLOBALS['TYPO3_CONF_VARS']['EXT'][$extKey]['templateLayouts'];
        }

        // Add TsConfig values
        foreach ($this->getTemplateLayoutsFromTsConfig($pageUid, $extKey) as $templateKey => $title) {
            if (GeneralUtility::isFirstPartOfStr($title, '--div--')) {
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
     * @return array
     */
    protected function getTemplateLayoutsFromTsConfig($pageUid, $extKey)
    {
        $templateLayouts = [];
        $pagesTsConfig = BackendUtility::getPagesTSconfig($pageUid);
        $extKey = 'tx_' . preg_replace('/_/', '', $extKey) . '.';
        if (isset($pagesTsConfig[$extKey]['templateLayouts.']) && is_array($pagesTsConfig[$extKey]['templateLayouts.'])) {
            $templateLayouts = $pagesTsConfig[$extKey]['templateLayouts.'];
        }
        return $templateLayouts;
    }
}
