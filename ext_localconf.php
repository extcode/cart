<?php

defined('TYPO3_MODE') or die();

// configure plugins

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'MiniCart',
    [
        'Cart' => 'showMiniCart, updateCurrency',
    ],
    // non-cacheable actions
    [
        'Cart' => 'showMiniCart, updateCurrency',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'Cart',
    [
        'Cart' => 'showCart, clearCart, addProduct, removeProduct, addCoupon, removeCoupon, setShipping, setPayment, updateCountry, updateCurrency, updateCart, orderCart, orderFinished',
        'Order' => 'paymentSuccess, paymentCancel',
    ],
    [
        'Cart' => 'showCart, clearCart, addProduct, removeProduct, addCoupon, removeCoupon, setShipping, setPayment, updateCountry, updateCurrency, updateCart, orderCart, orderFinished',
        'Order' => 'paymentSuccess, paymentCancel',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'Currency',
    [
        'Cart' => 'editCurrency, updateCurrency',
    ],
    [
        'Cart' => 'editCurrency, updateCurrency',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'Product',
    [
        'Product' => 'show, list, teaser, showForm',
    ],
    [
        'Product' => 'list, showForm',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'ProductPartial',
    [
        'Product' => 'showForm',
    ],
    [
        'Product' => 'showForm',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'FlexProduct',
    [
        'Product' => 'flexform',
    ],
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
    [
        'Order' => 'list, show',
    ]
);

// Icon Registry

if (TYPO3_MODE === 'BE') {
    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );

    $iconRegistry->registerIcon(
        'icon-apps-pagetree-page-cart-cart',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:cart/Resources/Public/Icons/icon-apps-pagetree-page-cart-cart.svg']
    );

    $iconRegistry->registerIcon(
        'icon-apps-pagetree-page-cart-product',
        \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
        ['source' => 'EXT:cart/Resources/Public/Icons/icon-apps-pagetree-page-cart-product.svg']
    );
}

// RealUrlHook

if (TYPO3_MODE === 'FE') {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['ConfigurationReader_postProc'][1499067085] =
        'EXT:cart/Classes/Hooks/RealUrlHook.php:Extcode\Cart\Hooks\RealUrlHook->postProcessConfiguration';
}

// ke_search indexer

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['registerIndexerConfiguration'][] = 'EXT:cart/Classes/Hooks/KeSearchIndexer.php:Extcode\Cart\Hooks\KeSearchIndexer';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_search']['customIndexer'][] = 'EXT:cart/Classes/Hooks/KeSearchIndexer.php:Extcode\Cart\Hooks\KeSearchIndexer';
