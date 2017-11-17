<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_payment',
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
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Payment.png'
    ],
    'hideTable' => 1,
    'interface' => [
        'showRecordFieldList' => 'service_country, service_id, name, provider, status, gross, net, tax, tax_class, note, transactions',
    ],
    'types' => [
        '1' => [
            'showitem' => '--palette--;' . $_LLL . ':tx_cart_domain_model_order_payment.palettes.service;service, name, provider, status, gross, net, tax, tax_class, note, transactions'
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
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.service_country',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => ''
            ],
        ],
        'service_id' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.service_id',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'int,required'
            ],
        ],
        'name' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.name',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'provider' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.provider',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim,required'
            ],
        ],
        'status' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$_LLL . ':tx_cart_domain_model_order_payment.status.open', 'open'],
                    [$_LLL . ':tx_cart_domain_model_order_payment.status.pending', 'pending'],
                    [$_LLL . ':tx_cart_domain_model_order_payment.status.paid', 'paid'],
                    [$_LLL . ':tx_cart_domain_model_order_payment.status.canceled', 'canceled']
                ],
                'size' => 1,
                'maxitems' => 1,
                'eval' => 'required'
            ],
        ],
        'gross' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.gross',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2'
            ],
        ],
        'net' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.net',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2'
            ],
        ],
        'tax' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.tax',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2'
            ],
        ],
        'tax_class' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.tax_class',
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
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.note',
            'config' => [
                'type' => 'text',
                'readOnly' => 1,
                'cols' => '40',
                'rows' => '15'
            ]
        ],

        'transactions' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.transactions',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_transaction',
                'foreign_field' => 'payment',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1,
                    'enabledControls' => [
                        'info' => false,
                        'new' => false,
                        'dragdrop' => false,
                        'sort' => false,
                        'hide' => false,
                        'delete' => false,
                        'localize' => false,
                    ],
                ],
            ],
        ],

        'item' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
