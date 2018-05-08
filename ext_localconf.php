<?php

defined('TYPO3_MODE') or die();

// configure plugins

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'MiniCart',
    [
        'Cart\CartPreview' => 'show',
        'Cart\Currency' => 'update',
    ],
    // non-cacheable actions
    [
        'Cart\CartPreview' => 'show',
        'Cart\Currency' => 'update',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'Cart',
    [
        'Cart\Cart' => 'show, clear, update',
        'Cart\Country' => 'update',
        'Cart\Coupon' => 'add, remove',
        'Cart\Currency' => 'update',
        'Cart\Order' => 'create',
        'Cart\Payment' => 'update',
        'Cart\Product' => 'add, remove',
        'Cart\Shipping' => 'update',
        'Order\Order' => 'update',
    ],
    [
        'Cart\Cart' => 'show, clear, update',
        'Cart\Country' => 'update',
        'Cart\Coupon' => 'add, remove',
        'Cart\Currency' => 'update',
        'Cart\Order' => 'create',
        'Cart\Payment' => 'update',
        'Cart\Product' => 'add, remove',
        'Cart\Shipping' => 'update',
        'Order\Order' => 'paymentSuccess, paymentCancel',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.' . $_EXTKEY,
    'Currency',
    [
        'Cart/Currency' => 'edit, update',
    ],
    [
        'Cart/Currency' => 'edit, update',
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
}

// register "cart:" namespace
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['cart'][]
    = 'Extcode\\Cart\\ViewHelpers';
