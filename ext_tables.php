<?php

defined('TYPO3_MODE') or die();

$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY);

$tcaPath = $extPath . 'Configuration/TCA/';
$iconPath = 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/';

$_LLL = 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    $_EXTKEY,
    'Configuration/TypoScript',
    'Shopping Cart - Cart'
);

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
    $pluginSignature = strtolower(str_replace('_', '', $_EXTKEY)) . '_' . strtolower($pluginName);
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
        'Extcode.' . $_EXTKEY,
        $pluginName,
        $_LLL . ':tx_cart.plugin.' . strtolower(preg_replace('/[A-Z]/', '_$0', lcfirst($pluginName)))
    );
    $TCA['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature] = 'select_key';

    $flexFormPath = 'EXT:' . $_EXTKEY . '/Configuration/FlexForms/' . $pluginName . 'Plugin.xml';
    if (file_exists(\TYPO3\CMS\Core\Utility\GeneralUtility::getFileAbsFileName($flexFormPath))) {
        $TCA['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue(
            $pluginSignature,
            'FILE:' . $flexFormPath
        );
    }
}
$TCA['tt_content']['types']['list']['subtypes_excludelist']['cart_minicart'] = 'select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_excludelist']['cart_currency'] = 'select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_excludelist']['cart_flexproduct'] = 'select_key,pages,recursive';

/**
 * Register Backend Modules
 */
if (TYPO3_MODE === 'BE') {
    if (!isset($TBE_MODULES['Cart'])) {
        $temp_TBE_MODULES = [];
        foreach ($TBE_MODULES as $key => $val) {
            if ($key == 'file') {
                $temp_TBE_MODULES[$key] = $val;
                $temp_TBE_MODULES['Cart'] = '';
            } else {
                $temp_TBE_MODULES[$key] = $val;
            }
        }

        $TBE_MODULES = $temp_TBE_MODULES;
    }

    // add Main Module
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Extcode.' . $_EXTKEY,
        'Cart',
        '',
        '',
        [],
        [
            'access' => 'user, group',
            'icon' => $iconPath . 'module.svg',
            'labels' => $_LLL . ':tx_cart.module.main',
            'navigationComponentId' => 'typo3-pagetree',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Extcode.' . $_EXTKEY,
        'Cart',
        'Orders',
        '',
        [
            'Backend\Order\Order' => 'list, export, show, generateNumber, generatePdfDocument, downloadPdfDocument',
            'Backend\Order\Payment' => 'update',
            'Backend\Order\Shipping' => 'update',
            'Backend\Order\Document' => 'download, create',
        ],
        [
            'access' => 'user, group',
            'icon' => $iconPath . 'module_orders.svg',
            'labels' => $_LLL . ':tx_cart.module.orders',
            'navigationComponentId' => 'typo3-pagetree',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Extcode.' . $_EXTKEY,
        'Cart',
        'OrderStatistics',
        '',
        [
            'Backend\Statistic' => 'show',
        ],
        [
            'access' => 'user, group',
            'icon' => $iconPath . 'module_order_statistics.svg',
            'labels' => $_LLL . ':tx_cart.module.order_statistics',
            'navigationComponentId' => 'typo3-pagetree',
        ]
    );
}

$TCA['pages']['ctrl']['typeicon_classes']['contains-coupons'] = 'apps-pagetree-folder-cart-coupons';
$TCA['pages']['ctrl']['typeicon_classes']['contains-orders'] = 'apps-pagetree-folder-cart-orders';

$TCA['pages']['columns']['module']['config']['items'][] = [
    'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_cart.module.orders',
    'orders',
    $iconPath . 'pagetree_folder_cart_orders.svg'
];
$TCA['pages']['columns']['module']['config']['items'][] = [
    'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:tx_cart.module.coupons',
    'coupons',
    $iconPath . 'pagetree_folder_cart_coupons.svg'
];
