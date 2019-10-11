<?php

defined('TYPO3_MODE') or die();

// configure plugins

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.cart',
    'MiniCart',
    [
        'Cart\CartPreview' => 'show',
        'Cart\Currency' => 'update',
    ],
    // non-cacheable actions
    [
//         'Cart\CartPreview' => 'show',
//         'Cart\Currency' => 'update',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.cart',
    'Cart',
    [
        'Cart\Cart' => 'show, clear, update',
        'Cart\Country' => 'update',
        'Cart\Coupon' => 'add, remove',
        'Cart\Currency' => 'update',
        'Cart\Order' => 'show, create',
        'Cart\Payment' => 'update',
        'Cart\Product' => 'add, remove',
        'Cart\Shipping' => 'update',
        'Order\Payment' => 'update',
    ],
    [
        'Cart\Cart' => 'show, clear, update',
        'Cart\Country' => 'update',
        'Cart\Coupon' => 'add, remove',
        'Cart\Currency' => 'update',
        'Cart\Order' => 'show, create',
        'Cart\Payment' => 'update',
        'Cart\Product' => 'add, remove',
        'Cart\Shipping' => 'update',
        'Order\Payment' => 'update',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.cart',
    'Currency',
    [
        'Cart\Currency' => 'edit, update',
    ],
    [
        'Cart\Currency' => 'edit, update',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Extcode.cart',
    'Order',
    [
        'Order\Order' => 'list, show',
    ],
    [
        'Order\Order' => 'list, show',
    ]
);

// Icon Registry

if (TYPO3_MODE === 'BE') {
    $icons = [
        'apps-pagetree-folder-cart-coupons' => 'apps_pagetree_folder_cart_coupons.svg',
        'apps-pagetree-folder-cart-orders' => 'apps_pagetree_folder_cart_orders.svg',
        'apps-pagetree-page-cart-cart' => 'apps_pagetree_page_cart_cart.svg',
        'ext-cart-wizard-icon' => 'cart_plugin_wizard.svg',
    ];

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );

    foreach ($icons as $identifier => $fileName) {
        $iconRegistry->registerIcon(
            $identifier,
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            [
                'source' => 'EXT:cart/Resources/Public/Icons/' . $fileName,
            ]
        );
    }

    $iconRegistry = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(
        \TYPO3\CMS\Core\Imaging\IconRegistry::class
    );
}

// TSconfig

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig('
    <INCLUDE_TYPOSCRIPT: source="FILE:EXT:cart/Configuration/TSconfig/ContentElementWizard.tsconfig">
');

// register "cart:" namespace
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['cart'][] = 'Extcode\\Cart\\ViewHelpers';

if (TYPO3_MODE === 'FE') {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['MailAttachmentsHook'][] =
        \Extcode\Cart\Hooks\MailAttachmentHook::class;
}
