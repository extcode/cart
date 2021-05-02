<?php

defined('TYPO3_MODE') or die();

// configure plugins

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Cart',
    'MiniCart',
    [
        \Extcode\Cart\Controller\Cart\CartPreviewController::class => 'show',
        \Extcode\Cart\Controller\Cart\CurrencyController::class => 'update',
    ],
    // non-cacheable actions
    [
        \Extcode\Cart\Controller\Cart\CartPreviewController::class => 'show',
        \Extcode\Cart\Controller\Cart\CurrencyController::class => 'update',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Cart',
    'Cart',
    [
        \Extcode\Cart\Controller\Cart\CartController::class => 'show, clear, update',
        \Extcode\Cart\Controller\Cart\CountryController::class => 'update',
        \Extcode\Cart\Controller\Cart\CouponController::class => 'add, remove',
        \Extcode\Cart\Controller\Cart\CurrencyController::class => 'update',
        \Extcode\Cart\Controller\Cart\OrderController::class => 'show, create',
        \Extcode\Cart\Controller\Cart\PaymentController::class => 'update',
        \Extcode\Cart\Controller\Cart\ProductController::class => 'add, remove',
        \Extcode\Cart\Controller\Cart\ShippingController::class => 'update',
        \Extcode\Cart\Controller\Order\PaymentController::class => 'update',
    ],
    [
        \Extcode\Cart\Controller\Cart\CartController::class => 'show, clear, update',
        \Extcode\Cart\Controller\Cart\CountryController::class => 'update',
        \Extcode\Cart\Controller\Cart\CouponController::class => 'add, remove',
        \Extcode\Cart\Controller\Cart\CurrencyController::class => 'update',
        \Extcode\Cart\Controller\Cart\OrderController::class => 'show, create',
        \Extcode\Cart\Controller\Cart\PaymentController::class => 'update',
        \Extcode\Cart\Controller\Cart\ProductController::class => 'add, remove',
        \Extcode\Cart\Controller\Cart\ShippingController::class => 'update',
        \Extcode\Cart\Controller\Order\PaymentController::class => 'update',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Cart',
    'Currency',
    [
        \Extcode\Cart\Controller\Cart\CurrencyController::class => 'edit, update',
    ],
    [
        \Extcode\Cart\Controller\Cart\CurrencyController::class => 'edit, update',
    ]
);

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
    'Cart',
    'Order',
    [
        \Extcode\Cart\Controller\Order\OrderController::class => 'list, show',
    ],
    [
        \Extcode\Cart\Controller\Order\OrderController::class => 'list, show',
    ]
);

// Feature Toggles

if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['SplitUpProcessOrderCreateEvent'])) {
    $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['SplitUpProcessOrderCreateEvent'] = false;
}

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

// view paths for TYPO3 Mail API
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths']['1588829280'] = 'EXT:cart/Resources/Private/Templates/';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths']['1588829280'] = 'EXT:cart/Resources/Private/Partials/';

// view paths for TYPO3 Dashboard
call_user_func(static function () {
    if (TYPO3_MODE === 'BE') {
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup(
            '
module.tx_dashboard {
    view {
        templateRootPaths.1588697552 = EXT:cart/Resources/Private/Templates/
        partialRootPaths.1588697552 = EXT:cart/Resources/Private/Partials/
        layoutRootPaths.1588697552 = EXT:cart/Resources/Private/Layouts/
    }
}'
        );
    }
});
