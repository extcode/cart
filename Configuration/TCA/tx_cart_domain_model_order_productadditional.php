<?php

defined('TYPO3') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_productadditional',
        'label' => 'additional_type',
        'label_alt' => 'additional_key, additional_value',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'additional_type,additional_key,additional_value',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/ProductAdditional.svg',
    ],
    'hideTable' => 1,
    'types' => [
        '1' => [
            'showitem' => 'additional_type, additional_key, additional_value, additional, additional_data',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'additional_type' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_productadditional.additional_type',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'additional_key' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_productadditional.additional_key',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'additional_value' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_productadditional.additional_value',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'additional' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_productadditional.additional',
            'config' => [
                'type' => 'text',
                'readOnly' => 1,
                'cols' => 48,
                'rows' => 15,
                'appearance' => [
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
        'additional_data' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_productadditional.additional_data',
            'config' => [
                'type' => 'text',
                'readOnly' => 1,
                'cols' => 48,
                'rows' => 15,
                'appearance' => [
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

        'product' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
