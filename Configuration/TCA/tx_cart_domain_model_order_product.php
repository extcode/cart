<?php

defined('TYPO3') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_product',
        'label' => 'title',
        'label_alt' => 'sku',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'sku,title',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Product.svg',
    ],
    'hideTable' => 1,
    'types' => [
        '1' => [
            'showitem' => '--palette--;;product_type_and_id, sku, title, count, --palette--;' . $_LLL . ':tx_cart_domain_model_order_product.price.group;price, product_additional, additional, additional_data',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
        'price' => [
            'showitem' => 'price, discount, --linebreak--, gross, net, --linebreak--, tax, tax_class',
            'canNotCollapse' => 1,
        ],
        'product_type_and_id' => [
            'showitem' => 'product_type, product_id',
            'canNotCollapse' => 1,
        ],
    ],
    'columns' => [
        'product_id' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.product_id',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'product_type' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.product_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['label' => '', 'value' => ''],
                ],
                'readOnly' => 1,
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'sku' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.sku',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'title' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.title',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
                'required' => true,
            ],
        ],
        'count' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.count',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'int',
            ],
        ],
        'price' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.price',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2',
            ],
        ],
        'discount' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.discount',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2',
            ],
        ],
        'gross' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.gross',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2',
            ],
        ],
        'net' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.net',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2',
            ],
        ],
        'tax' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.tax',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2',
            ],
        ],
        'tax_class' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.tax_class',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_taxclass',
                'minitems' => 1,
                'maxitems' => 1,
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
        'additional' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.additional',
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
            'label' => $_LLL . ':tx_cart_domain_model_order_product.additional_data',
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

        'product_additional' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_product.product_additional',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_productadditional',
                'foreign_field' => 'product',
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
