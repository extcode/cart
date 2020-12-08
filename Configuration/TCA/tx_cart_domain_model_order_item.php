<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_order_item',
        'label' => 'order_number',
        'label_alt' => 'invoice_number',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

        'versioningWS' => 2,
        'versioning_followPages' => true,
        'origUid' => 't3_origuid',
        'delete' => 'deleted',
        'enablecolumns' => [],
        'searchFields' => 'order_number, invoice_number',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Order/Item.svg'
    ],
    'interface' => [
        'showRecordFieldList' => 'pid, cart_pid, fe_user, order_number, invoice_number, delivery_number, billing_address, shipping_address, gross, net, total_gross, total_net, additional, additional_data, tax_class, products, discounts, tax, total_tax, payment, shipping, comment',
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
                --palette--;' . $_LLL . ':tx_cart_domain_model_order_item.palettes.documents;pdfs'
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => ''
        ],
        'addresses' => [
            'showitem' => 'billing_address, shipping_address',
            'canNotCollapse' => 0
        ],
        'numbers' => [
            'showitem' => 'order_number, order_date, --linebreak--, invoice_number, invoice_date, --linebreak--, delivery_number, delivery_date',
            'canNotCollapse' => 1
        ],
        'price' => [
            'showitem' => 'currency, --linebreak--, currency_code, currency_sign, currency_translation, --linebreak--, gross, net, --linebreak--, order_tax',
            'canNotCollapse' => 1
        ],
        'total_price' => [
            'showitem' => 'total_gross, total_net, --linebreak--, order_total_tax',
            'canNotCollapse' => 1
        ],
        'pdfs' => [
            'showitem' => 'order_pdfs, --linebreak--, invoice_pdfs, --linebreak--, delivery_pdfs',
            'canNotCollapse' => 1
        ]
    ],
    'columns' => [
        'pid' => [
            'exclude' => 1,
            'config' => [
                'type' => 'passthrough'
            ]
        ],
        'cart_pid' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => $_LLL . ':tx_cart_domain_model_category.cart_pid',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'pages',
                'size' => 1,
                'maxitems' => 1,
                'minitems' => 0,
                'show_thumbs' => 1,
                'default' => 0,
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                        'default' => [
                            'searchWholePhrase' => true
                        ]
                    ],
                ],
            ]
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
                    [$_LLL . ':tx_cart_domain_model_order_item.fe_user.not_available', 0],
                ],
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],
        'order_number' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.order_number',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'order_date' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.order_date',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0'
            ]
        ],
        'invoice_number' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.invoice_number',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'invoice_date' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.invoice_date',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0'
            ]
        ],
        'delivery_number' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.delivery_number',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'trim'
            ],
        ],
        'delivery_date' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.delivery_date',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0'
            ]
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
                    'record_type' => '\\' . \Extcode\Cart\Domain\Model\Order\BillingAddress::class
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
                    'record_type' => '\\' . \Extcode\Cart\Domain\Model\Order\ShippingAddress::class
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
                'eval' => 'required'
            ],
        ],
        'currency_code' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.currency_code',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'required'
            ],
        ],
        'currency_sign' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.currency_sign',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'required'
            ],
        ],
        'currency_translation' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.currency_translation',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2,required'
            ],
        ],
        'gross' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.gross',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2,required'
            ],
        ],
        'total_gross' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.total_gross',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2,required'
            ],
        ],
        'net' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.net',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2,required'
            ],
        ],
        'total_net' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.total_net',
            'config' => [
                'type' => 'input',
                'readOnly' => 1,
                'size' => 30,
                'eval' => 'double2,required'
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
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'file',
                [
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
                    'foreign_match_fields' => [
                        'fieldname' => 'order_pdfs',
                        'tablenames' => 'tx_cart_domain_model_order_item',
                        'table_local' => 'sys_file',
                    ],
                    'maxitems' => 99,
                ],
                'pdf'
            ),
        ],
        'invoice_pdfs' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.invoice_pdfs',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'file',
                [
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
                    'foreign_match_fields' => [
                        'fieldname' => 'invoice_pdfs',
                        'tablenames' => 'tx_cart_domain_model_order_item',
                        'table_local' => 'sys_file',
                    ],
                    'maxitems' => 99,
                ],
                'pdf'
            ),
        ],
        'delivery_pdfs' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_order_item.delivery_pdfs',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'file',
                [
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
                    'foreign_match_fields' => [
                        'fieldname' => 'delivery_pdfs',
                        'tablenames' => 'tx_cart_domain_model_order_item',
                        'table_local' => 'sys_file',
                    ],
                    'maxitems' => 99,
                ],
                'pdf'
            ),
        ],
        'crdate' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'config' => [
                'type' => 'input',
                'size' => '8',
                'max' => '20',
                'eval' => 'date',
                'checkbox' => '0',
                'default' => '0'
            ]
        ],
    ],
];
