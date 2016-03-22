<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_product_coupon',
        'label' => 'code',
        'label_alt' => 'title',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'title',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Product/Coupon.png'
    ],
    'hideTable' => 1,
    'interface' => [
        'showRecordFieldList' => 'title, code, discount, tax_class_id, cart_min_price, is_combinable, is_relative_discount, number_available, number_used',
    ],
    'types' => [
        '1' => [
            'showitem' => 'title, code, discount, tax_class_id, cart_min_price, is_combinable, number_available, number_used'
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => ''
        ],
    ],
    'columns' => [
        'title' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_product_coupon.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'code' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_product_coupon.code',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'discount' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_product_coupon.discount',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ],
        ],
        'tax_class_id' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_coupon.tax_class_id',
            'config' => [
                'type' => 'select',
                'items' => [
                    [$_LLL . ':tx_cart_domain_model_product_coupon.tax_class_id.1', 1],
                ],
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'cart_min_price' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_product_coupon.cart_min_price',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ],
        ],
        'is_combinable' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_coupon.is_combinable',
            'config' => [
                'type' => 'check',
            ],
        ],
        'is_relative_discount' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_coupon.is_relative_discount',
            'config' => [
                'type' => 'check',
            ],
        ],
        'number_available' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_product_coupon.number_available',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ],
        ],
        'number_used' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_product_coupon.number_used',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ],
        ],
    ],
];
