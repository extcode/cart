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
use Extcode\Cart\Domain\Log\DatabaseWriter;
use TYPO3\CMS\Core\Log\LogLevel;
use TYPO3\CMS\Core\Utility\ArrayUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

(static function (string $extKey) {
    if (is_array($GLOBALS['TYPO3_CONF_VARS'] ?? null) === false) {
        throw new Exception('$GLOBALS[\'TYPO3_CONF_VARS\'] is not an array', 1774601240);
    }

    ArrayUtility::mergeRecursiveWithOverrule(
        $GLOBALS['TYPO3_CONF_VARS'],
        [
            'LOG' => [
                'Extcode' => [
                    'Cart' => [
                        'Domain' => [
                            'Log' => [
                                'LogService' => [
                                    'writerConfiguration' => [
                                        LogLevel::INFO => [
                                            DatabaseWriter::class => [],
                                        ],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            // view paths for TYPO3 Mail API
            'MAIL' => [
                'templateRootPaths' => [
                    '1588829280' => 'EXT:cart/Resources/Private/Templates/',
                ],
                'partialRootPaths' => [
                    '1588829280' => 'EXT:cart/Resources/Private/Partials/',
                ],
            ],
            'SYS' => [
                'fluid' => [
                    'namespaces' => [
                        'cart' => [
                            1 => 'Extcode\\Cart\\ViewHelpers',
                        ],
                    ],
                ],
            ],
        ]
    );

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

})('cart');
