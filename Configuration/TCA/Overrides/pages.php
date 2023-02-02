<?php

defined('TYPO3') or die();

call_user_func(function () {
    $_LLL_be = 'LLL:EXT:cart/Resources/Private/Language/locallang_be.xlf:';
    $_LLL_db = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

    $GLOBALS['TCA']['pages']['columns']['doktype']['config']['items'][] = [
        $_LLL_db . 'pages.doktype.181',
        181,
        'apps-pagetree-page-cart-cart',
    ];
    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
        $_LLL_be . 'tcarecords-pages-contains.cart_coupons',
        'coupons',
        'apps-pagetree-folder-cart-coupons',
    ];
    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
        $_LLL_be . 'tcarecords-pages-contains.cart_orders',
        'orders',
        'apps-pagetree-folder-cart-orders',
    ];

    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][181] = 'apps-pagetree-page-cart-cart';
    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-coupons'] = 'apps-pagetree-folder-cart-coupons';
    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-orders'] = 'apps-pagetree-folder-cart-orders';
});
