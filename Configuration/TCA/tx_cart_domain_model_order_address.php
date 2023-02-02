<?php

use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;

defined('TYPO3') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_address',
        'label' => 'uid',
        'label_alt' => 'first_name, last_name, street, street_number, zip, city',
        'label_alt_force' => 1,
        'type' => 'record_type',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'first_name, last_name, street, zip, city',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Address.svg',
    ],
    'hideTable' => 1,
    'types' => [
        '\\' . BillingAddress::class => [
            'showitem' => 'title, salutation, first_name, last_name, email, phone, fax, company, street, addition, zip, city, country, phone, fax, additional, tax_identification_number',
        ],
        '\\' . ShippingAddress::class => [
            'showitem' => 'title, salutation, first_name, last_name, email, phone, fax, company, street, addition, zip, city, country, phone, fax, additional',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
    ],
    'columns' => [
        'record_type' => [
            'label' => $_LLL . ':tx_cart_domain_model_order_address.record_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'readOnly' => 1,
                'items' => [
                    ['label' => $_LLL . ':tx_cart_domain_model_order_address.record_type.billing', 'value' => '\\' . BillingAddress::class],
                    ['label' => $_LLL . ':tx_cart_domain_model_order_address.record_type.shipping', 'value' => '\\' . ShippingAddress::class],
                ],
                'default' => '\\' . BillingAddress::class,
            ],
        ],
        'title' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.title',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'salutation' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.salutation',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'first_name' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.first_name',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'last_name' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.last_name',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'email' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.email',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'company' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.company',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'tax_identification_number' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.tax_identification_number',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'street' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.street',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'street_number' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.street_number',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'addition' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.addition',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'zip' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.zip',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'city' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.city',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'country' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.country',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'phone' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.phone',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'fax' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.fax',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'additional' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_address.additional',
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

        'item' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
