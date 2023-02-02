<?php

declare(strict_types=1);

use Extcode\Cart\Controller\Backend\Order\DocumentController;
use Extcode\Cart\Controller\Backend\Order\OrderController;
use Extcode\Cart\Controller\Backend\Order\PaymentController;
use Extcode\Cart\Controller\Backend\Order\ShippingController;

$_LLL_db = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

/**
 * Definitions for modules provided by EXT:cart
 */
return [
    'cart_cart_main' => [
        'access' => 'user, group',
        'workspaces' => 'live',
        'path' => '/module/cart',
        'labels' => $_LLL_db . 'tx_cart.module.main',
        'extensionName' => 'Cart',
        'iconIdentifier' => 'ext-cart-module',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
    ],
    'cart_cart_orders' => [
        'parent' => 'cart_cart_main',
        'position' => ['before' => '*'],
        'access' => 'user, group',
        'workspaces' => 'live',
        'path' => '/module/cart/orders',
        'labels' => [
            'title' => $_LLL_db . 'tx_cart.module.orders',
        ],
        'extensionName' => 'Cart',
        'controllerActions' => [
            OrderController::class => 'list, export, show, generateNumber, generatePdfDocument, downloadPdfDocument',
            PaymentController::class => 'update',
            ShippingController::class => 'update',
            DocumentController::class => 'download, create',
        ],
        'iconIdentifier' => 'ext-cart-module-order',
        'navigationComponent' => '@typo3/backend/page-tree/page-tree-element',
    ],
];
