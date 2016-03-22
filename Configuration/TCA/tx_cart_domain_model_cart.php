<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_cart',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => '',
    ],
    'interface' => [
        'showRecordFieldList' => 'pid, fe_user, was_ordered, order_item, cart',
    ],
    'types' => [
        '1' => [
            'showitem' =>
                'pid, fe_user, was_ordered, order_item, cart'
        ],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
    'columns' => [
        'pid' => [
            'exclude' => 1,
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'fe_user' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_cart.fe_user',
            'config' => [
                'type' => 'select',
                'readOnly' => 1,
                'foreign_table' => 'fe_users',
                'size' => 1,
                'autoMaxSize' => 1,
                'minitems' => 0,
                'maxitems' => 1,
                'multiple' => 0,
            ]
        ],
        'was_ordered' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_cart.was_ordered',
            'config' => [
                'type' => 'check',
            ],
        ],
        'order_item' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_cart.order_item',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_item',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'cart' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_cart.cart',
            'config' => [
                'type' => 'text',
                'cols' => 48,
                'rows' => 15,
                'eval' => 'required',
            ],
        ],
    ],
];
