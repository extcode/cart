<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_product_product',
        'label' => 'sku',
        'label_alt' => 'title',
        'label_alt_force' => 1,
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'dividers2tabs' => true,

        'versioningWS' => 2,
        'versioning_followPages' => true,

        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'requestUpdate' => 'product_type, be_variant_attribute1, be_variant_attribute2, be_variant_attribute3, handle_stock, handle_stock_in_variants',
        'searchFields' => 'sku,title,teaser,description,price,',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Product/Product.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, product_type, sku, title, header_image, teaser, description, min_number_in_order, max_number_in_order, price, special_prices, price_measure, price_measure_unit, base_price_measure_unit, service_attribute1, service_attribute2, service_attribute3, tax_class_id, be_variant_attribute1, be_variant_attribute2, be_variant_attribute3, fe_variants, be_variants, related_products, categories, tags',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1, starttime, endtime,
            product_type, sku, title,
            --div--;' . $_LLL . ':tx_cart_domain_model_product_product.div.descriptions,
                teaser;;;richtext:rte_transform[mode=ts_links], description;;;richtext:rte_transform[mode=ts_links],
                product_content,
            --div--;' . $_LLL . ':tx_cart_domain_model_product_product.div.images_and_files,
                header_image, images, files,
            --div--;' . $_LLL . ':tx_cart_domain_model_product_product.div.prices,
                --palette--;' . $_LLL . ':tx_cart_domain_model_product_product.palette.minmax;minmax,
                --palette--;' . $_LLL . ':tx_cart_domain_model_product_product.palette.prices;prices,
                --palette--;' . $_LLL . ':tx_cart_domain_model_product_product.palette.measures;measures,
                --palette--;' . $_LLL . ':tx_cart_domain_model_product_product.palette.service_attributes;service_attributes,
            --div--;' . $_LLL . ':tx_cart_domain_model_product_product.div.stock,
                handle_stock, handle_stock_in_variants, stock,
            --div--;' . $_LLL . ':tx_cart_domain_model_product_product.div.variants,
                fe_variants,
                --palette--;' . $_LLL . ':tx_cart_domain_model_product_product.palette.be_variant_attributes;be_variant_attributes,
            --div--;' . $_LLL . ':tx_cart_domain_model_product_product.div.related_products,
                related_products,  
            --div--;' . $_LLL . ':tx_cart_domain_model_product_product.div.tags_categories,
                tags, categories'
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => ''
        ],
        'be_variant_attributes' => [
            'showitem' => 'be_variant_attribute1, be_variant_attribute2, be_variant_attribute3, --linebreak--, be_variants',
            'canNotCollapse' => 1
        ],
        'minmax' => [
            'showitem' => 'min_number_in_order, max_number_in_order',
            'canNotCollapse' => 1
        ],
        'prices' => [
            'showitem' => 'price, tax_class_id, --linebreak--, special_prices',
            'canNotCollapse' => 1
        ],
        'measures' => [
            'showitem' => 'price_measure, price_measure_unit, base_price_measure_unit',
            'canNotCollapse' => 1
        ],
        'service_attributes' => [
            'showitem' => 'service_attribute1, service_attribute2, service_attribute3',
            'canNotCollapse' => 1
        ],
    ],
    'columns' => [

        'sys_language_uid' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.language',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_language',
                'foreign_table_where' => 'ORDER BY sys_language.title',
                'items' => [
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.allLanguages', -1],
                    ['LLL:EXT:lang/locallang_general.xlf:LGL.default_value', 0]
                ],
            ],
        ],
        'l10n_parent' => [
            'displayCond' => 'FIELD:sys_language_uid:>:0',
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.l18n_parent',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_cart_domain_model_product_product',
                'foreign_table_where' => 'AND tx_cart_domain_model_product_product.pid=###CURRENT_PID### AND tx_cart_domain_model_product_product.sys_language_uid IN (-1,0)',
            ],
        ],
        'l10n_diffsource' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
        't3ver_label' => [
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.versionLabel',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'max' => 255,
            ]
        ],
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
            ],
        ],
        'starttime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.starttime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],
        'endtime' => [
            'exclude' => 1,
            'l10n_mode' => 'mergeIfNotBlank',
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.endtime',
            'config' => [
                'type' => 'input',
                'size' => 13,
                'max' => 20,
                'eval' => 'datetime',
                'checkbox' => 0,
                'default' => 0,
                'range' => [
                    'lower' => mktime(0, 0, 0, date('m'), date('d'), date('Y'))
                ],
            ],
        ],

        'product_type' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.product_type',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$_LLL . ':tx_cart_domain_model_product_product.product_type.simple', 'simple'],
                    [$_LLL . ':tx_cart_domain_model_product_product.product_type.configurable', 'configurable'],
                    [$_LLL . ':tx_cart_domain_model_product_product.product_type.virtual', 'virtual'],
                    [$_LLL . ':tx_cart_domain_model_product_product.product_type.downloadable', 'downloadable'],
                ],
                'default' => 'simple',
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],

        'sku' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.sku',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required,trim'
            ],
        ],

        'title' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.title',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required,trim'
            ],
        ],

        'teaser' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.teaser',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ],
            'defaultExtras' => 'richtext[]'
        ],

        'description' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.description',
            'config' => [
                'type' => 'text',
                'cols' => 40,
                'rows' => 15,
                'eval' => 'trim'
            ],
            'defaultExtras' => 'richtext[]'
        ],

        'min_number_in_order' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.min_number_in_order',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'int'
            ]
        ],

        'max_number_in_order' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.max_number_in_order',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'int'
            ]
        ],

        'price' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.price',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required,double2',
                'default' => '0.00',
            ]
        ],

        'special_prices' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.special_prices',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_cart_domain_model_product_specialprice',
                'foreign_field' => 'product',
                'foreign_table_where' => ' AND tx_cart_domain_model_product_specialprice.pid=###CURRENT_PID### ORDER BY tx_cart_domain_model_product_specialprice.title ',
                'maxitems' => 99,
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],

        'price_measure' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.price_measure',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2',
                'default' => '0.00',
            ]
        ],

        'price_measure_unit' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.price_measure_unit',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$_LLL . ':tx_cart_domain_model_product_product.measure.no_measuring_unit', 0],
                    [$_LLL . ':tx_cart_domain_model_product_product.measure.weight', '--div--'],
                    ['mg', 'mg'],
                    ['g', 'g'],
                    ['kg', 'kg'],
                    [$_LLL . ':tx_cart_domain_model_product_product.measure.volume', '--div--'],
                    ['ml', 'ml'],
                    ['cl', 'cl'],
                    ['l', 'l'],
                    ['cbm', 'cbm'],
                    [$_LLL . ':tx_cart_domain_model_product_product.measure.length', '--div--'],
                    ['cm', 'cm'],
                    ['m', 'm'],
                    [$_LLL . ':tx_cart_domain_model_product_product.measure.area', '--div--'],
                    ['m²', 'm2'],
                ],
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],

        'base_price_measure_unit' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.base_price_measure_unit',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$_LLL . ':tx_cart_domain_model_product_product.measure.no_measuring_unit', 0],
                    [$_LLL . ':tx_cart_domain_model_product_product.measure.weight', '--div--'],
                    ['mg', 'mg'],
                    ['g', 'g'],
                    ['kg', 'kg'],
                    [$_LLL . ':tx_cart_domain_model_product_product.measure.volume', '--div--'],
                    ['ml', 'ml'],
                    ['cl', 'cl'],
                    ['l', 'l'],
                    ['cbm', 'cbm'],
                    [$_LLL . ':tx_cart_domain_model_product_product.measure.length', '--div--'],
                    ['cm', 'cm'],
                    ['m', 'm'],
                    [$_LLL . ':tx_cart_domain_model_product_product.measure.area', '--div--'],
                    ['m²', 'm2'],
                ],
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],

        'service_attribute1' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.service_attribute1',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2,required',
                'default' => '0.00',
            ]
        ],
        'service_attribute2' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.service_attribute2',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2,required',
                'default' => '0.00',
            ]
        ],
        'service_attribute3' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.service_attribute3',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2,required',
                'default' => '0.00',
            ]
        ],

        'tax_class_id' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.tax_class_id',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$_LLL . ':tx_cart_domain_model_product_product.tax_class_id.1', 1],
                    [$_LLL . ':tx_cart_domain_model_product_product.tax_class_id.2', 2],
                    [$_LLL . ':tx_cart_domain_model_product_product.tax_class_id.3', 3],
                ],
                'size' => 1,
                'minitems' => 1,
                'maxitems' => 1,
            ],
        ],

        'be_variant_attribute1' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:product_type:=:configurable',
            'label' => $_LLL . ':tx_cart_domain_model_product_product.be_variant_attribute1',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_cart_domain_model_product_bevariantattribute',
                'foreign_table_where' => ' AND tx_cart_domain_model_product_bevariantattribute.pid=###CURRENT_PID### ORDER BY tx_cart_domain_model_product_bevariantattribute.title ',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],

        'be_variant_attribute2' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:product_type:=:configurable',
            'label' => $_LLL . ':tx_cart_domain_model_product_product.be_variant_attribute2',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_cart_domain_model_product_bevariantattribute',
                'foreign_table_where' => ' AND tx_cart_domain_model_product_bevariantattribute.pid=###CURRENT_PID### ORDER BY tx_cart_domain_model_product_bevariantattribute.title ',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],

        'be_variant_attribute3' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:product_type:=:configurable',
            'label' => $_LLL . ':tx_cart_domain_model_product_product.be_variant_attribute3',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_cart_domain_model_product_bevariantattribute',
                'foreign_table_where' => ' AND tx_cart_domain_model_product_bevariantattribute.pid=###CURRENT_PID### ORDER BY tx_cart_domain_model_product_bevariantattribute.title ',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],

        'fe_variants' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.fe_variants',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_cart_domain_model_product_fevariant',
                'foreign_field' => 'product',
                'foreign_table_where' => ' AND tx_cart_domain_model_product_fevariant.pid=###CURRENT_PID### ORDER BY tx_cart_domain_model_product_fevariant.title ',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],

        'be_variants' => [
            'exclude' => 1,
            'displayCond' => 'FIELD:product_type:=:configurable',
            'label' => $_LLL . ':tx_cart_domain_model_product_product.be_variants',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_cart_domain_model_product_bevariant',
                'foreign_field' => 'product',
                'foreign_table_where' => ' AND tx_cart_domain_model_product_bevariant.pid=###CURRENT_PID### ORDER BY tx_cart_domain_model_product_bevariant.title ',
                'maxitems' => 9999,
                'appearance' => [
                    'collapseAll' => 1,
                    'levelLinksPosition' => 'top',
                    'showSynchronizationLink' => 1,
                    'showPossibleLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1
                ],
            ],
        ],

        'related_products' => [
            'exclude' => 1,
            'label' => $_LLL . 'tx_cart_domain_model_product_product.related_products',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cart_domain_model_product_product',
                'foreign_table' => 'tx_cart_domain_model_product_product',
                'MM_opposite_field' => 'related_products_from',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 100,
                'MM' => 'tx_cart_domain_model_product_product_related_mm',
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

        'related_products_from' => [
            'exclude' => 1,
            'label' => $_LLL . 'tx_cart_domain_model_product_product.related_products_from',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'foreign_table' => 'tx_cart_domain_model_product_product',
                'allowed' => 'tx_cart_domain_model_product_product',
                'size' => 5,
                'maxitems' => 100,
                'MM' => 'tx_cart_domain_model_product_product_related_mm',
                'readOnly' => 1,
            ]
        ],

        'images' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.images',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'image',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
                    ],
                    'minitems' => 0,
                    'maxitems' => 99,
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],

        'files' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.files',
            'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
                'files',
                [
                    'appearance' => [
                        'createNewRelationLinkTitle' => 'LLL:EXT:cms/locallang_ttc.xlf:images.addFileReference',
                    ],
                    'minitems' => 0,
                    'maxitems' => 99,
                ],
                $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
            ),
        ],

        'product_content' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.product_content',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tt_content',
                'foreign_field' => 'tx_cart_domain_model_product_product',
                'foreign_sortby' => 'sorting',
                'minitems' => 0,
                'maxitems' => 99,
                'appearance' => [
                    'levelLinksPosition' => 'top',
                    'showPossibleLocalizationRecords' => true,
                    'showRemovedLocalizationRecords' => true,
                    'showAllLocalizationLink' => true,
                    'showSynchronizationLink' => true,
                    'enabledControls' => [
                        'info' => true,
                        'new' => true,
                        'dragdrop' => false,
                        'sort' => true,
                        'hide' => true,
                        'delete' => true,
                        'localize' => true,
                    ]
                ],
                'inline' => [
                    'inlineNewButtonStyle' => 'display: inline-block;',
                ],
                'behaviour' => [
                    'localizationMode' => 'select',
                    'localizeChildrenAtParentLocalization' => true,
                ],
            ]
        ],

        'handle_stock' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.handle_stock',
            'config' => [
                'type' => 'check',
            ],
        ],
        'handle_stock_in_variants' => [
            'exclude' => 1,
            'displayCond' => [
                'AND' => [
                    'FIELD:product_type:=:configurable',
                    'FIELD:handle_stock:=:1',
                ]
            ],
            'label' => $_LLL . ':tx_cart_domain_model_product_product.handle_stock_in_variants',
            'config' => [
                'type' => 'check',
            ],
        ],
        'stock' => [
            'exclude' => 1,
            'displayCond' => [
                'AND' => [
                    'FIELD:handle_stock:=:1',
                    'FIELD:handle_stock_in_variants:=:0',
                ]
            ],
            'label' => $_LLL . ':tx_cart_domain_model_product_product.stock',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'int',
                'default' => 0,
            ]
        ],

        'tags' => [
            'exclude' => 1,
            'label' => $_LLL . 'tx_cart_domain_model_product_product.tags',
            'config' => [
                'type' => 'group',
                'internal_type' => 'db',
                'allowed' => 'tx_cart_domain_model_product_tag',
                'foreign_table' => 'tx_cart_domain_model_product_tag',
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 100,
                'MM' => 'tx_cart_domain_model_product_tag_mm',
                'wizards' => [
                    'suggest' => [
                        'type' => 'suggest',
                        'default' => [
                            'searchWholePhrase' => true
                        ]
                    ],
                ],
            ],
        ],
    ],
];
