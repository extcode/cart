<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';
$_LLL_product = $_LLL . ':tx_cart_domain_model_product_product';

return [
    'ctrl' => [
        'title' => $_LLL . ':tx_cart_domain_model_product_bevariant',
        'label' => 'be_variant_attribute_option1',
        'label_alt' => 'be_variant_attribute_option2,be_variant_attribute_option3',
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
        'hideTable' => true,
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
            'starttime' => 'starttime',
            'endtime' => 'endtime',
        ],
        'searchFields' => 'title',
        'iconfile' => 'EXT:cart/Resources/Public/Icons/Product/BeVariant.png'
    ],
    'interface' => [
        'showRecordFieldList' => 'sys_language_uid, l10n_parent, l10n_diffsource, hidden, be_variant_attribute_option1, be_variant_attribute_option2, be_variant_attribute_option3, stock',
    ],
    'types' => [
        '1' => [
            'showitem' => 'sys_language_uid;;;;1-1-1, l10n_parent, l10n_diffsource, hidden;;1,
        --palette--;' . $_LLL . ':tx_cart_domain_model_product_bevariant.palette.variants;variants,
        --div--;' . $_LLL_product . '.div.prices,
            --palette--;' . $_LLL_product . '.palette.prices;prices,
            --palette--;' . $_LLL_product . '.palette.measure;measure,
            stock,
        --div--;LLL:EXT:cms/locallang_ttc.xlf:tabs.access, starttime, endtime'
        ],
    ],
    'palettes' => [
        '1' => [
            'showitem' => ''
        ],
        'variants' => [
            'showitem' => 'be_variant_attribute_option1, be_variant_attribute_option2, be_variant_attribute_option3',
            'canNotCollapse' => 1
        ],
        'prices' => ['showitem' => 'price, price_calc_method, --linebreak--, special_prices', 'canNotCollapse' => 1],
        'measure' => ['showitem' => 'price_measure, price_measure_unit', 'canNotCollapse' => 1],
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
                'foreign_table' => 'tx_cart_domain_model_product_bevariant',
                'foreign_table_where' => 'AND tx_cart_domain_model_product_bevariant.pid=###CURRENT_PID### AND tx_cart_domain_model_product_bevariant.sys_language_uid IN (-1,0)',
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

        'be_variant_attribute_option1' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_product_bevariant.be_variant_attribute_option1',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_cart_domain_model_product_bevariantattributeoption',
                'foreign_table_where' =>
                    ' AND tx_cart_domain_model_product_bevariantattributeoption.pid=###CURRENT_PID###' .
                    ' AND tx_cart_domain_model_product_bevariantattributeoption.be_variant_attribute IN ((SELECT tx_cart_domain_model_product_product.be_variant_attribute1 FROM tx_cart_domain_model_product_product WHERE tx_cart_domain_model_product_product.uid=###REC_FIELD_product###)) ' .
                    ' ORDER BY tx_cart_domain_model_product_bevariantattributeoption.title ',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],

        'be_variant_attribute_option2' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_product_bevariant.be_variant_attribute_option2',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_cart_domain_model_product_bevariantattributeoption',
                'foreign_table_where' =>
                    ' AND tx_cart_domain_model_product_bevariantattributeoption.pid=###CURRENT_PID###' .
                    ' AND tx_cart_domain_model_product_bevariantattributeoption.be_variant_attribute IN ((SELECT tx_cart_domain_model_product_product.be_variant_attribute2 FROM tx_cart_domain_model_product_product WHERE tx_cart_domain_model_product_product.uid=###REC_FIELD_product###)) ' .
                    ' ORDER BY tx_cart_domain_model_product_bevariantattributeoption.title ',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],

        'be_variant_attribute_option3' => [
            'exclude' => 0,
            'label' => $_LLL . ':tx_cart_domain_model_product_bevariant.be_variant_attribute_option3',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    ['', 0],
                ],
                'foreign_table' => 'tx_cart_domain_model_product_bevariantattributeoption',
                'foreign_table_where' =>
                    ' AND tx_cart_domain_model_product_bevariantattributeoption.pid=###CURRENT_PID###' .
                    ' AND tx_cart_domain_model_product_bevariantattributeoption.be_variant_attribute IN ((SELECT tx_cart_domain_model_product_product.be_variant_attribute3 FROM tx_cart_domain_model_product_product WHERE tx_cart_domain_model_product_product.uid=###REC_FIELD_product###)) ' .
                    ' ORDER BY tx_cart_domain_model_product_bevariantattributeoption.title ',
                'minitems' => 0,
                'maxitems' => 1,
            ],
        ],

        'price' => [
            'exclude' => 1,
            'label' => $_LLL_product . '.price',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required,double2',
                'default' => '0.00',
            ]
        ],

        'price_calc_method' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_bevariant.price_calc_method',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$_LLL . ':tx_cart_domain_model_product_bevariant.price_calc_method.0', 0],
                    [$_LLL . ':tx_cart_domain_model_product_bevariant.price_calc_method.1', 1],
                    [$_LLL . ':tx_cart_domain_model_product_bevariant.price_calc_method.2', 2],
                    [$_LLL . ':tx_cart_domain_model_product_bevariant.price_calc_method.3', 3],
                    [$_LLL . ':tx_cart_domain_model_product_bevariant.price_calc_method.4', 4],
                    [$_LLL . ':tx_cart_domain_model_product_bevariant.price_calc_method.5', 5]
                ],
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],

        'special_prices' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_product.special_prices',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_cart_domain_model_product_specialprice',
                'foreign_field' => 'be_variant',
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
            'label' => $_LLL_product . '.price_measure',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'double2'
            ]
        ],

        'price_measure_unit' => [
            'exclude' => 1,
            'label' => $_LLL_product . '.price_measure_unit',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectSingle',
                'items' => [
                    [$_LLL_product . '.measure.no_measuring_unit', 0],
                    [$_LLL_product . '.measure.weight', '--div--'],
                    ['mg', 'mg'],
                    ['g', 'g'],
                    ['kg', 'kg'],
                    [$_LLL_product . '.measure.volume', '--div--'],
                    ['ml', 'ml'],
                    ['cl', 'cl'],
                    ['l', 'l'],
                    ['cbm', 'cbm'],
                    [$_LLL_product . '.measure.length', '--div--'],
                    ['cm', 'cm'],
                    ['m', 'm'],
                    [$_LLL_product . '.measure.area'],
                    ['m²', 'm2'],
                ],
                'size' => 1,
                'minitems' => 0,
                'maxitems' => 1,
            ]
        ],

        'stock' => [
            'exclude' => 1,
            'label' => $_LLL . ':tx_cart_domain_model_product_bevariant.stock',
            'config' => [
                'type' => 'input',
                'size' => 30,
                'eval' => 'required,int',
                'default' => 0,
            ]
        ],

        'product' => [
            'config' => [
                'type' => 'passthrough',
            ],
        ],
    ],
];
