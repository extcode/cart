<?php

defined('TYPO3_MODE') or die();

$_LLL_db = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

/**
 * Register Backend Modules
 */
if (TYPO3_MODE === 'BE') {
    // add Main Module
    \TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
        'Cart',
        'Cart',
        '',
        '',
        [],
        [
            'access' => 'user, group',
            'iconIdentifier' => 'ext-cart-module',
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
            'iconIdentifier' => 'ext-cart-module-order',
            'labels' => $_LLL_db . 'tx_cart.module.orders',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
        ]
    );
}
