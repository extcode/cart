<?php

defined('TYPO3') or die();

use Extcode\Cart\Domain\Model\Cart\CartCouponPercentage;

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_coupon',
        'label' => 'code',
        'label_alt' => 'title',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'origUid' => 't3_origuid',

        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/tx_cart_domain_model_coupon.svg',
    ],
    'hideTable' => 1,
    'types' => [
        '1' => [
            'showitem' => 'hidden,--palette--;;1,starttime,endtime,title,code,coupon_type,discount,tax_class_id,cart_min_price,is_combinable,handle_available,number_available,number_used',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:core/Resources/Private/Language/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'renderType' => 'checkboxToggle',
                'default' => 0,
                'items' => [
                    [
                        0 => '',
                        1 => '',
                    ],
                ],
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:starttime_formlabel',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
                'renderType' => 'inputDateTime',
                ['behaviour' => ['allowLanguageSynchronization' => true]],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:endtime_formlabel',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y')),
                ],
                'renderType' => 'inputDateTime',
                ['behaviour' => ['allowLanguageSynchronization' => true]],
            ],
        ],
        'title' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_coupon.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'code' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_coupon.code',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'coupon_type' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_coupon.coupon_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $_LLL . ':tx_cart_domain_model_coupon.coupon_type.cartdiscount.fix', 'value' => 'cartdiscount'],
                    ['label' => $_LLL . ':tx_cart_domain_model_coupon.coupon_type.cartdiscount.percentage', 'value' => CartCouponPercentage::class],
                ],
                'default' => 'cartdiscount',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
            'onChange' => 'reload',
        ],
        'discount' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_coupon.discount',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2',
            ],
        ],
        'tax_class_id' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_coupon.tax_class_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $_LLL . ':tx_cart_domain_model_coupon.tax_class_id.1', 'value' => 1],
                ],
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'cart_min_price' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_coupon.cart_min_price',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2',
            ],
        ],
        'is_combinable' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:coupon_type:=:cartdiscount',
            'label' => $_LLL . ':tx_cart_domain_model_coupon.is_combinable',
            'config' => [
                'type' => 'check',
            ],
        ],
        'is_relative_discount' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_coupon.is_relative_discount',
            'config' => [
                'type' => 'check',
            ],
        ],
        'handle_available' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_coupon.handle_available',
            'config' => [
                'type' => 'check',
            ],
        ],
        'number_available' => [
            'exclude' => 0,
            'displayCond' => 'FIELD:handle_available:REQ:TRUE',
            'label' => $_LLL . ':tx_cart_domain_model_coupon.number_available',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'int',
                'default' => '0',
            ],
        ],
        'number_used' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_coupon.number_used',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'int',
                'default' => '0',
            ],
        ],
    ],
];
