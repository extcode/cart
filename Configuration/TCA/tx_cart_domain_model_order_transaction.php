<?php

defined('TYPO3') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_transaction',
        'label' => 'txn_id',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'txn_id',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Transaction.svg',
    ],
    'hideTable' => 1,
    'types' => [
        '1' => [
            'showitem' => 'txn_id, status, external_status_code, note',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'txn_id' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_transaction.txn_id',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'txn_txt' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_transaction.txn_txt',
            'config' => [
                'type' => 'text',
                'readOnly' => 1,
                'cols' => '40',
                'rows' => '15',
            ],
        ],
        'status' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_transaction.status',
            'config' => [
                'type' => 'select',
                'readOnly' => 1,
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => $_LLL . ':tx_cart_domain_model_order_transaction.status.unknown', 'value' => 'unknown'],
                    ['label' => $_LLL . ':tx_cart_domain_model_order_transaction.status.invalid', 'value' => 'invalid'],
                    ['label' => $_LLL . ':tx_cart_domain_model_order_transaction.status.open', 'value' => 'open'],
                    ['label' => $_LLL . ':tx_cart_domain_model_order_transaction.status.pending', 'value' => 'pending'],
                    ['label' => $_LLL . ':tx_cart_domain_model_order_transaction.status.paid', 'value' => 'paid'],
                    ['label' => $_LLL . ':tx_cart_domain_model_order_transaction.status.canceled', 'value' => 'canceled'],
                ],
                'size' => 1,
                'maxitems' => 1,
                'required' => true,
            ],
        ],
        'external_status_code' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_transaction.external_status_code',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'note' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_transaction.note',
            'config' => [
                'type' => 'text',
                'readOnly' => 1,
                'cols' => '40',
                'rows' => '15',
            ],
        ],

        'payment' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
