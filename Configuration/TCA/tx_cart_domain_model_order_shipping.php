<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_shipping',
        'label' => 'name',
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
        'searchFields' => 'name,value,calc,sum,',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Shipping.png'
    ],
    'hideTable' => 1,
    'interface' => [
        'showRecordFieldList' => 'service_country, service_id, name, status, gross, net, tax, tax_class, note',
    ],
    'types' => [
        '1' => [
            'showitem' => '--palette--;' . $_LLL . ':tx_cart_domain_model_order_shipping.palettes.service;service, name, status, gross, net, tax, tax_class, note'
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => ''
        ],
        'service' => [
            'showitem' => 'service_country, service_id',
            'canNotCollapse' => 0
        ],
    ],
    'columns' => [
        'service_country' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.service_country',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => ''
            ],
        ],
        'service_id' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.service_id',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'int,required'
            ],
        ],
        'name' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.name',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'status' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$_LLL . ':tx_cart_domain_model_order_shipping.status.open', 'open'],
                    [$_LLL . ':tx_cart_domain_model_order_shipping.status.on_hold', 'on_hold'],
                    [$_LLL . ':tx_cart_domain_model_order_shipping.status.in_process', 'in_process'],
                    [$_LLL . ':tx_cart_domain_model_order_shipping.status.shipped', 'shipped']
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ],
        ],
        'gross' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.gross',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2'
            ],
        ],
        'net' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.net',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2'
            ],
        ],
        'tax' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.tax',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2'
            ],
        ],
        'tax_class' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.tax_class',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_taxclass',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],
        'note' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.note',
            'config' => [
                'type' => 'text',
                'readOnly' => 1,
                'cols' => '40',
                'rows' => '15'
            ]
        ],

        'item' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
