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
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
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
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

ExtensionUtility::configurePlugin(
    'Cart',
    'Currency',
    [
        CurrencyController::class => 'edit, update',
    ],
    [
        CurrencyController::class => 'edit, update',
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

ExtensionUtility::configurePlugin(
    'Cart',
    'Order',
    [
        \Extcode\Cart\Controller\Order\OrderController::class => 'list, show',
    ],
    [
        \Extcode\Cart\Controller\Order\OrderController::class => 'list, show',
    ],
    ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
);

// register "cart:" namespace
$GLOBALS['TYPO3_CONF_VARS']['SYS']['fluid']['namespaces']['cart'][] = 'Extcode\\Cart\\ViewHelpers';

// view paths for TYPO3 Mail API
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['templateRootPaths']['1588829280'] = 'EXT:cart/Resources/Private/Templates/';
$GLOBALS['TYPO3_CONF_VARS']['MAIL']['partialRootPaths']['1588829280'] = 'EXT:cart/Resources/Private/Partials/';
