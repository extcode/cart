<?php

declare(strict_types=1);

use Extcode\Cart\Configuration\Constants;
use TYPO3\CMS\Core\Domain\Repository\PageRepository;
use TYPO3\CMS\Core\Utility\ArrayUtility;

defined('TYPO3') or die();

(static function (): void {
    $_LLL_db = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

    ArrayUtility::mergeRecursiveWithOverrule(
        $GLOBALS['TCA']['pages'],
        [
            'columns' => [
                'doktype' => [
                    'config' => [
                        'items' => [
                            1776199422 => [
                                'label' => $_LLL_db . 'pages.doktype.' . Constants::DOKTYPE_CART_CART,
                                'value' => Constants::DOKTYPE_CART_CART,
                                'icon' => 'apps-pagetree-page-cart-cart',
                                'group' => 'default',
                            ],
                        ],
                    ],
                ],
                'module' => [
                    'config' => [
                        'items' => [
                            1776199535 => [
                                'label' => $_LLL_db . 'tcarecords-pages-contains.cart_coupons',
                                'value' => 'coupons',
                                'icon' => 'apps-pagetree-folder-cart-coupons',
                            ],
                            1776199573 => [
                                'label' => $_LLL_db . 'tcarecords-pages-contains.cart_orders',
                                'value' => 'orders',
                                'icon' => 'apps-pagetree-folder-cart-orders',
                            ],
                        ],
                    ],
                ],
            ],
            'ctrl' => [
                'typeicon_classes' => [
                    Constants::DOKTYPE_CART_CART => 'apps-pagetree-page-cart-cart',
                    'contains-coupons' => 'apps-pagetree-folder-cart-coupons',
                    'contains-orders' => 'apps-pagetree-folder-cart-orders',
                ],
            ],
            'types' => [
                Constants::DOKTYPE_CART_CART => $GLOBALS['TCA']['pages']['types'][(string) PageRepository::DOKTYPE_DEFAULT],
            ],
        ]
    );
})();
