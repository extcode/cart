<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'MiniCart',
    [
        'Cart' => 'showMiniCart',
    ],
    // non-cacheable actions
    [
        'Cart' => 'showMiniCart',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'Cart',
    [
        'Cart' => 'showCart, clearCart, addProduct, removeProduct, addCoupon, removeCoupon, setShipping, setPayment, updateCart, orderCart',
    ],
    // non-cacheable actions
    [
        'Cart' => 'showCart, clearCart, addProduct, removeProduct, addCoupon, removeCoupon, setShipping, setPayment, updateCart, orderCart',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'Product',
    [
        'Product' => 'show, list, teaser',
    ],
    // non-cacheable actions
    [
        'Product' => 'list',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'FlexProduct',
    [
        'Product' => 'flexform',
    ],
    // non-cacheable actions
    [
        'Product' => '',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'Order',
    [
        'Order' => 'list, show',
    ],
    // non-cacheable actions
    [
        'Order' => 'list, show',
    ]
);

if (TYPO3_MODE == 'FE') {
    $TYPO3_CONF_VARS['FE']['eID_include']['addProduct'] = 'EXT:cart/Classes/Utility/eIDDispatcher.php';
}