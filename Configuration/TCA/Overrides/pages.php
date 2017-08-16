<?php
defined('TYPO3_MODE') or die();

call_user_func(function () {
    $_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

    $GLOBALS['TCA']['pages']['columns']['doktype']['config']['items'][] = [
        $_LLL . 'pages.doktype.181',
        181,
        'icon-apps-pagetree-page-cart-cart'
    ];

    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][181] = 'icon-apps-pagetree-page-cart-cart';

    $GLOBALS['TCA']['pages']['columns']['doktype']['config']['items'][] = [
        $_LLL . 'pages.doktype.182',
        182,
        'icon-apps-pagetree-page-cart-product'
    ];

    $GLOBALS['TCA']['pages']['ctrl']['typeicon_classes'][182] = 'icon-apps-pagetree-page-cart-product';
});
