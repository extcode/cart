<?php

defined('TYPO3_MODE') or die();

use Extcode\Cart\Controller\Backend\Order\DocumentController;
use Extcode\Cart\Controller\Backend\Order\OrderController;
use Extcode\Cart\Controller\Backend\Order\PaymentController;
use Extcode\Cart\Controller\Backend\Order\ShippingController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

$_LLL_db = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

/**
 * Register Backend Modules
 */
if (TYPO3_MODE === 'BE') {
    // add Main Module
    ExtensionUtility::registerModule(
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

    ExtensionUtility::registerModule(
        'Cart',
        'Cart',
        'Orders',
        '',
        [
            OrderController::class => 'list, export, show, generateNumber, generatePdfDocument, downloadPdfDocument',
            PaymentController::class => 'update',
            ShippingController::class => 'update',
            DocumentController::class => 'download, create',
        ],
        [
            'access' => 'user, group',
            'iconIdentifier' => 'ext-cart-module-order',
            'labels' => $_LLL_db . 'tx_cart.module.orders',
            'navigationComponentId' => 'TYPO3/CMS/Backend/PageTree/PageTreeElement',
        ]
    );
}
