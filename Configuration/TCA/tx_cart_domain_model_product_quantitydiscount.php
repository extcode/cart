<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_product_quantitydiscount',
        'label' => 'quantity',
        'label_alt' => 'price',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

        'versioningWS' => 2,
        'versioning_followPages' => true,

        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'fe_group' => 'frontend_user_group'
        ],
        'searchFields' => 'price',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Product/QuantityDiscount.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden, frontend_user_group, quantity, price',
    ],
    'types' => [
        '1' => ['showitem' => 'hidden;;1, frontend_user_group, quantity, price'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [

        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ]
        ],

        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],

        'quantity' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_quantitydiscount.quantity',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required,int',
                'default' => '0',
            ]
        ],
        'price' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_quantitydiscount.price',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required,double2',
                'default' => '0.00',
            ]
        ],

        'frontend_user_group' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_quantitydiscount.frontend_user_group',
            'config' => [
                'type' => 'select',
                'readOnly' => 0,
                'foreign_table' => 'fe_groups',
                'size' => 1,
                'items' => [
                    ['', 0],
                ],
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],

        'product' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
