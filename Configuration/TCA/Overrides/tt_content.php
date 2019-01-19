<?php
defined('TYPO3_MODE') or die();

call_user_func(function () {
    $_LLL_db = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

    /**
     * Register Frontend Plugins
     */
    $pluginNames = [
        'MiniCart',
        'Cart',
        'Currency',
        'Order',
    ];

    foreach ($pluginNames as $pluginName) {
        $pluginSignature = 'cart_' . strtolower($pluginName);
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
            'Extcode.cart',
            $pluginName,
            $_LLL_db . 'tx_cart.plugin.' . strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($pluginName)))
        );
        $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key';

        $flexFormPath = 'EXT:cart/Configuration/FlexForms/' . $pluginName . 'Plugin.xml';
        if (file_exists(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($flexFormPath))) {
            $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
            \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
                $pluginSignature,
                'FILE:' . $flexFormPath
            );
        }
    }

    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['cart_minicart'] = 'select_key,pages,recursive';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['cart_currency'] = 'select_key,pages,recursive';
    $GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist']['cart_flexproduct'] = 'select_key,pages,recursive';
});
