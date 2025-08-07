<?php

defined('TYPO3') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_tax',
        'label' => 'tax_class',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => '',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Tax.svg',
        'type' => 'record_type',
    ],
    'hideTable' => 1,
    'types' => [
        'tax' => [
            'showitem' => 'tax, tax_class',
        ],
        'total_tax' => [
            'showitem' => 'tax, tax_class',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'record_type' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_tax.record_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [
                        'label' => $_LLL . ':tx_cart_domain_model_order_tax.record_type.tax',
                        'value' => 'tax',
                    ],
                    [
                        'label' => $_LLL . ':tx_cart_domain_model_order_tax.record_type.total_tax',
                        'value' => 'total_tax',
                    ],
                ],
                'default' => 'tax',
            ],
        ],
        'tax' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_tax.tax',
            'config' => [
                'type' => 'number',
                'readOnly' => 1,
                'size' => 30,
                'format' => 'decimal',
            ],
        ],
        'tax_class' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_tax.tax_class',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_taxclass',
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],

        'item' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
