<?php

defined('TYPO3_MODE') or die();

$iconPath = 'EXT:cart/Resources/Public/Icons/';
$_LLL_db = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

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
        'Extcode.cart',
        'Cart',
        '',
        '',
        [],
        [
            'access' => 'user, group',
            'icon' => $iconPath . 'module.svg',
            'labels' => $_LLL_db . 'tx_cart.module.main',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Extcode.cart',
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
            'labels' => $_LLL_db . 'tx_cart.module.orders',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Extcode.cart',
        'Cart',
        'OrderStatistics',
        '',
        [
            'Backend\Statistic' => 'show',
        ],
        [
            'access' => 'user, group',
            'icon' => $iconPath . 'module_order_statistics.svg',
            'labels' => $_LLL_db . 'tx_cart.module.order_statistics',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
        ]
    );
}
