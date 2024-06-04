<?php

use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;

defined('TYPO3') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_item',
        'label' => 'order_number',
        'label_alt' => 'invoice_number',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',

        'versioningWS' => true,
        'origUid' => 't3_origuid',
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'order_number, invoice_number',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Item.svg',
    ],
    'types' => [
        '1' => [
            'showitem' =>
                'pid, fe_user,
                --palette--;' . $_LLL . ':tx_cart_domain_model_order_item.palettes.numbers;numbers,
                --palette--;' . $_LLL . ':tx_cart_domain_model_order_item.palettes.addresses;addresses,
                --palette--;' . $_LLL . ':tx_cart_domain_model_order_item.palettes.price;price,
                --palette--;' . $_LLL . ':tx_cart_domain_model_order_item.palettes.total_price;total_price,
                comment,
                additional,
                additional_data,
                tax_class,
                products,
                discounts,
                payment,
                shipping,
                --palette--;' . $_LLL . ':tx_cart_domain_model_order_item.palettes.documents;pdfs',
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => '',
        ],
        'addresses' => [
            'showitem' => 'billing_address, shipping_address',
        ],
        'numbers' => [
            'showitem' => 'order_number, order_date, --linebreak--, invoice_number, invoice_date, --linebreak--, delivery_number, delivery_date',
        ],
        'price' => [
            'showitem' => 'currency, --linebreak--, currency_code, currency_sign, currency_translation, --linebreak--, gross, net, --linebreak--, tax',
        ],
        'total_price' => [
            'showitem' => 'total_gross, total_net, --linebreak--, total_tax',
        ],
        'pdfs' => [
            'showitem' => 'order_pdfs, --linebreak--, invoice_pdfs, --linebreak--, delivery_pdfs',
        ],
    ],
    'columns' => [
        'pid' => [
            'exclude' => 1,
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        'cart_pid' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_category.cart_pid',
            'config' => [
                'type' => 'group',
                'allowed' => 'pages',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'default' => 0,
                ['behaviour' => ['allowLanguageSynchronization' => true]],
                'suggestOptions' => [
                    'default' => [
                        'searchWholePhrase' => true,
                    ],
                ],
            ],
        ],
        'fe_user' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.fe_user',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'readOnly' => 1,
                'foreign_table' => 'fe_users',
                'size' => 1,
                'items' => [
                    ['label' => $_LLL . ':tx_cart_domain_model_order_item.fe_user.not_available', 'value' => 0],
                ],
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],
        'order_number' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.order_number',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'order_date' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.order_date',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'renderType' => 'inputDateTime',
            ],
        ],
        'invoice_number' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.invoice_number',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'invoice_date' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.invoice_date',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'renderType' => 'inputDateTime',
            ],
        ],
        'delivery_number' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.delivery_number',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim',
            ],
        ],
        'delivery_date' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.delivery_date',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'renderType' => 'inputDateTime',
            ],
        ],
        'billing_address' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.billing_address',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_address',
                'foreign_field' => 'item',
                'foreign_match_fields' => [
                    'record_type' => '\\' . BillingAddress::class,
                ],
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
        'shipping_address' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.shipping_address',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_address',
                'foreign_field' => 'item',
                'foreign_match_fields' => [
                    'record_type' => '\\' . ShippingAddress::class,
                ],
                'minitems' => 0,
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
        'comment' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.comment',
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
        'additional' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.additional',
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
            'label' => $_LLL . ':tx_cart_domain_model_order_item.additional_data',
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
        'tax_class' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.tax_class',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_taxclass',
                'foreign_field' => 'item',
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
        'currency' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.currency',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'required' => true,
            ],
        ],
        'currency_code' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.currency_code',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'required' => true,
            ],
        ],
        'currency_sign' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.currency_sign',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'required' => true,
            ],
        ],
        'currency_translation' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.currency_translation',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2',
                'required' => true,
            ],
        ],
        'gross' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.gross',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2',
                'required' => true,
            ],
        ],
        'total_gross' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.total_gross',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2',
                'required' => true,
            ],
        ],
        'net' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.net',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2',
                'required' => true,
            ],
        ],
        'total_net' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.total_net',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2',
                'required' => true,
            ],
        ],
        'tax' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.tax',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_tax',
                'foreign_field' => 'item',
                'foreign_match_fields' => [
                    'record_type' => 'tax',
                ],
                'maxitems' => 9999,
                'default' => 0,
            ],
        ],
        'total_tax' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.total_tax',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_tax',
                'foreign_field' => 'item',
                'foreign_match_fields' => [
                    'record_type' => 'total_tax',
                ],
                'maxitems' => 9999,
                'default' => 0,
            ],
        ],
        'products' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.products',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_product',
                'foreign_field' => 'item',
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
        'discounts' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.discounts',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_discount',
                'foreign_field' => 'item',
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
        'payment' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.payment',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_payment',
                'foreign_field' => 'item',
                'minitems' => 0,
                'maxitems' => 1,
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
        'shipping' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.shipping',
            'config' => [
                'type' => 'inline',
                'readOnly' => 1,
                'foreign_table' => 'tx_cart_domain_model_order_shipping',
                'foreign_field' => 'item',
                'minitems' => 0,
                'maxitems' => 1,
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
        'order_pdfs' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.order_pdfs',
            'config' => [
                'type' => 'file',
                'appearance' => [
                    'enabledControls' => [
                        'info' => true,
                        'new' => false,
                        'dragdrop' => false,
                        'sort' => false,
                        'hide' => false,
                        'delete' => true,
                        'localize' => false,
                    ],
                ],
                'maxitems' => 99,
                'allowed' => ['pdf'],
            ],
        ],
        'invoice_pdfs' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.invoice_pdfs',
            'config' => [
                'type' => 'file',
                'appearance' => [
                    'enabledControls' => [
                        'info' => true,
                        'new' => false,
                        'dragdrop' => false,
                        'sort' => false,
                        'hide' => false,
                        'delete' => true,
                        'localize' => false,
                    ],
                ],
                'maxitems' => 99,
                'allowed' => ['pdf'],
            ],
        ],
        'delivery_pdfs' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.delivery_pdfs',
            'config' => [
                'type' => 'file',
                'appearance' => [
                    'enabledControls' => [
                        'info' => true,
                        'new' => false,
                        'dragdrop' => false,
                        'sort' => false,
                        'hide' => false,
                        'delete' => true,
                        'localize' => false,
                    ],
                ],
                'maxitems' => 99,
                'allowed' => ['pdf'],
            ],
        ],
        'crdate' => [
            'exclude' => 1,
            'config' => [
                'type' => 'input',
                'size' => '8',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0',
                'renderType' => 'inputDateTime',
                ['behaviour' => ['allowLanguageSynchronization' => true]],
            ],
        ],
    ],
];
