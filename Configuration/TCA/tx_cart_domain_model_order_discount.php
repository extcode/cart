<?php

defined('TYPO3') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_discount',
        'label' => 'code',
        'label_alt' => 'title',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'title',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Discount.svg',
    ],
    'hideTable' => 1,
    'types' => [
        '1' => [
            'showitem' => 'title, code, gross, net, tax_class_id, tax',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'title' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_discount.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'code' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_discount.code',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'gross' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_discount.gross',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2',
            ],
        ],
        'net' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_discount.net',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2',
            ],
        ],
        'tax_class_id' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_order_discount.tax_class_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $_LLL . ':tx_cart_domain_model_order_discount.tax_class_id.1', 'value' => 1],
                ],
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'tax' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_discount.tax',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2',
            ],
        ],

        'item' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
