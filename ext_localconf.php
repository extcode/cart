<?php

defined('TYPO3_MODE') or die();

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'MiniCart',
    [
        'Cart' => 'showMini',
    ],
    // non-cacheable actions
    [
        'Cart' => 'showMini',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'Cart',
    [
        'Cart' => 'showCart, clearCart, addProduct, removeProduct, addCoupon, setShipping, setPayment, updateCart, orderCart',
    ],
    // non-cacheable actions
    [
        'Cart' => 'showCart, clearCart, addProduct, removeProduct, addCoupon, setShipping, setPayment, updateCart, orderCart',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'Product',
    [
        'Product' => 'show, list, teaser, flexform',
    ],
    // non-cacheable actions
    [
        'Product' => 'show',
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

$TYPO3_CONF_VARS['FE']['eID_include']['addProduct'] = 'EXT:cart/eid/addProduct.php';
