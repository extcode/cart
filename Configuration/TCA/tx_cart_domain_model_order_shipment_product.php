<?php

declare(strict_types=1);

defined('TYPO3') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_shipment_product',
        'label' => 'order_product',
        'label_alt' => 'count',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Product.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => 'order_product, count',
        ],
    ],
    'columns' => [
        'order_product' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment_product.order_product',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_product',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'count' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment_product.count',
            'config' => [
                'type' => 'number',
                'readOnly' => 1,
                'size' => 8,
                'default' => 0,
            ],
        ],
        'shipment' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
