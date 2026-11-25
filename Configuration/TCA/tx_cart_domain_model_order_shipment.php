<?php

declare(strict_types=1);

use Extcode\Cart\Domain\Order\Status\ServiceType;
use Extcode\Cart\Hooks\OrderStatusItemsProcFunc;

defined('TYPO3') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_shipment',
        'label' => 'uid',
        'label_alt' => 'status, tracking_number',
        'label_alt_force' => true,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Shipping.svg',
    ],
    'types' => [
        '1' => [
            'showitem' => 'status, replacement, carrier, tracking_number, tracking_url, shipped_date, note, products',
        ],
    ],
    'columns' => [
        'status' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.status',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'readOnly' => 1,
                'items' => [],
                'itemsProcFunc' => OrderStatusItemsProcFunc::class . '->addItems',
                'itemsProcConfig' => [
                    'serviceType' => ServiceType::Shipment->value,
                ],
                'size' => 1,
                'maxitems' => 1,
            ],
        ],
        'replacement' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.replacement',
            'config' => [
                'type' => 'check',
                'readOnly' => 1,
            ],
        ],
        'carrier' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.carrier',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
            ],
        ],
        'tracking_number' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.tracking_number',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
            ],
        ],
        'tracking_url' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.tracking_url',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
            ],
        ],
        'shipped_date' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.shipped_date',
            'config' => [
                'type' => 'datetime',
                'readOnly' => 1,
                'default' => 0,
            ],
        ],
        'note' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.note',
            'config' => [
                'type' => 'text',
                'readOnly' => 1,
                'cols' => 40,
                'rows' => 5,
            ],
        ],
        'products' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.products',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_shipment_product',
                'foreign_field' => 'shipment',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 1,
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
