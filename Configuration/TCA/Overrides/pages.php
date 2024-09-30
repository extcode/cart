<?php

defined('TYPO3') or die();

call_user_func(function () {
    $_LLL_db = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

    $GLOBALS['TCA']['pages']['columns']['doktype']['config']['items'][] = [
        'label' => $_LLL_db . 'pages.doktype.181',
        'value' => 181,
        'icon' => 'apps-pagetree-page-cart-cart',
    ];
    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
        'label' => $_LLL_db . 'tcarecords-pages-contains.cart_coupons',
        'value' => 'coupons',
        'icon' => 'apps-pagetree-folder-cart-coupons',
    ];
    $GLOBALS['TCA']['pages']['columns']['module']['config']['items'][] = [
        'label' => $_LLL_db . 'tcarecords-pages-contains.cart_orders',
        'value' => 'orders',
        'icon' => 'apps-pagetree-folder-cart-orders',
    ];

    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][181] = 'apps-pagetree-page-cart-cart';
    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-coupons'] = 'apps-pagetree-folder-cart-coupons';
    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes']['contains-orders'] = 'apps-pagetree-folder-cart-orders';
});
