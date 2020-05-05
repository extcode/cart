<?php

defined('TYPO3_MODE') or die();

$_LLL_db = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

/**
 * Register Backend Modules
 */
if (TYPO3_MODE === 'BE') {
    if (!isset($GLOBALS['TBE_MODULES']['_configuration']['Cart'])) {
        $temp_TBE_MODULES = [];

        foreach ($GLOBALS['TBE_MODULES']['_configuration'] as $key => $val) {
            $temp_TBE_MODULES[$key] = $val;
            if ($key === 'file') {
                $temp_TBE_MODULES['Cart'] = '';
            }
        }

        $GLOBALS['TBE_MODULES']['_configuration'] = $temp_TBE_MODULES;
    }

    // add Main Module
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Cart',
        'Cart',
        '',
        '',
        [],
        [
            'access' => 'user, group',
            'icon' => 'EXT:cart/Resources/Public/Icons/module.svg',
            'labels' => $_LLL_db . 'tx_cart.module.main',
        ]
    );

    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Cart',
        'Cart',
        'Orders',
        '',
        [
            \Extcode\Cart\Controller\Backend\Order\OrderController::class => 'list, export, show, generateNumber, generatePdfDocument, downloadPdfDocument',
            \Extcode\Cart\Controller\Backend\Order\PaymentController::class => 'update',
            \Extcode\Cart\Controller\Backend\Order\ShippingController::class => 'update',
            \Extcode\Cart\Controller\Backend\Order\DocumentController::class => 'download, create',
        ],
        [
            'access' => 'user, group',
            'icon' => 'EXT:cart/Resources/Public/Icons/module_orders.svg',
            'labels' => $_LLL_db . 'tx_cart.module.orders',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
        ]
    );
}
