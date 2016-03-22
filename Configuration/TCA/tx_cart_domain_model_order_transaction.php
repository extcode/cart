<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_transaction',
        'label' => 'txn_id',
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
        'searchFields' => 'txn_id',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Transaction.png'
    ],
    'hideTable' => 1,
    'interface' => [
        'showRecordFieldList' => 'txn_id',
    ],
    'types' => [
        '1' => [
            'showitem' => 'txn_id'
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => ''
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
                'eval' => 'trim'
            ],
        ],
        'payment' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
