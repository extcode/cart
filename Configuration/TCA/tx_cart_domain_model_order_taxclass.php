<?php

defined('TYPO3') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_taxclass',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'title,value,calc',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/TaxClass.svg',
    ],
    'hideTable' => 1,
    'types' => [
        '1' => [
            'showitem' => 'title, value, calc',
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
            'label' => $_LLL . ':tx_cart_domain_model_order_taxclass.title',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'value' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_taxclass.value',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'calc' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_taxclass.calc',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
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
