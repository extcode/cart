<?php

defined('TYPO3') or die();

use Extcode\Cart\Controller\Cart\CartController;
use Extcode\Cart\Controller\Cart\CartPreviewController;
use Extcode\Cart\Controller\Cart\CountryController;
use Extcode\Cart\Controller\Cart\CouponController;
use Extcode\Cart\Controller\Cart\CurrencyController;
use Extcode\Cart\Controller\Cart\OrderController;
use Extcode\Cart\Controller\Cart\PaymentController;
use Extcode\Cart\Controller\Cart\ProductController;
use Extcode\Cart\Controller\Cart\ShippingController;
use Extcode\Cart\Hooks\MailAttachmentHook;
use TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider;
use TYPO3\CMS\Core\Imaging\IconRegistry;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

// configure plugins

ExtensionUtility::configurePlugin(
    'Cart',
    'MiniCart',
    [
        CartPreviewController::class => 'show',
        CurrencyController::class => 'update',
    ],
    [
        CartPreviewController::class => 'show',
        CurrencyController::class => 'update',
    ]
);

ExtensionUtility::configurePlugin(
    'Cart',
    'Cart',
    [
        CartController::class => 'show, clear, update',
        CountryController::class => 'update',
        CouponController::class => 'add, remove',
        CurrencyController::class => 'update',
        OrderController::class => 'show, create',
        PaymentController::class => 'update',
        ProductController::class => 'add, remove',
        ShippingController::class => 'update',
    ],
    [
        CartController::class => 'show, clear, update',
        CountryController::class => 'update',
        CouponController::class => 'add, remove',
        CurrencyController::class => 'update',
        OrderController::class => 'show, create',
        PaymentController::class => 'update',
        ProductController::class => 'add, remove',
        ShippingController::class => 'update',
    ]
);

ExtensionUtility::configurePlugin(
    'Cart',
    'Currency',
    [
        CurrencyController::class => 'edit, update',
    ],
    [
        CurrencyController::class => 'edit, update',
    ]
);

ExtensionUtility::configurePlugin(
    'Cart',
    'Order',
    [
        \Extcode\Cart\Controller\Order\OrderController::class => 'list, show',
    ],
    [
        \Extcode\Cart\Controller\Order\OrderController::class => 'list, show',
    ]
);

// Icon Registry

$icons = [
    'apps-pagetree-folder-cart-coupons' => 'apps_pagetree_folder_cart_coupons.svg',
    'apps-pagetree-folder-cart-orders' => 'apps_pagetree_folder_cart_orders.svg',
    'apps-pagetree-page-cart-cart' => 'apps_pagetree_page_cart_cart.svg',
    'ext-cart-wizard-icon' => 'cart_plugin_wizard.svg',
    'ext-cart-module' => 'module.svg',
    'ext-cart-module-order' => 'module_orders.svg',
];

$iconRegistry = GeneralUtility::makeInstance(
    IconRegistry::class
);

foreach ($icons as $identifier => $fileName) {
    $iconRegistry->registerIcon(
        $identifier,
        SvgIconProvider::class,
        [
            'source' => 'EXT:cart/Resources/Public/Icons/' . $fileName,
        ]
    );
}

// TSconfig

ExtensionManagementUtility::addPageTSConfig('
    <INCLUDE_TYPOSCRIPT: source="FILE:EXT:cart/Configuration/TSconfig/ContentElementWizard.tsconfig">
');

// register "cart:" namespace
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['cart'][] = 'Extcode\\Cart\\ViewHelpers';

// view paths for TYPO3 Mail API
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths']['1588829280'] = 'EXT:cart/Resources/Private/Templates/';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths']['1588829280'] = 'EXT:cart/Resources/Private/Partials/';

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cart']['MailAttachmentsHook'][] = MailAttachmentHook::class;
