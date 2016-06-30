<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

$newSysCategoryColumns = [
    'images' => [
        'exclude' => 1,
        'l10n_mode' => 'mergeIfNotBlank',
        'label' => $_LLL . ':tx_cart_domain_model_category.image',
        'config' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getFileFieldTCAConfig(
            'images',
            [
                'appearance' => [
                    'createNewRelationLinkTitle' => 'LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:images.addFileReference',
                    'showPossibleLocalizationRecords' => 1,
                    'showRemovedLocalizationRecords' => 1,
                    'showAllLocalizationLink' => 1,
                    'showSynchronizationLink' => 1
                ],
                'foreign_match_fields' => [
                    'fieldname' => 'images',
                    'tablenames' => 'sys_category',
                    'table_local' => 'sys_file',
                ],
            ],
            $GLOBALS['TYPO3_CONF_VARS']['GFX']['imagefile_ext']
        )
    ],
    'cart_product_single_pid' => [
        'exclude' => 1,
        'l10n_mode' => 'mergeIfNotBlank',
        'label' => $_LLL . ':tx_cart_domain_model_category.cart_product_single_pid',
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
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_category', $newSysCategoryColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_category',
    '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_tca.xlf:pages.tabs.options, images',
    '',
    'before:description'
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'sys_category',
    'cart_product_single_pid',
    '',
    'after:description'
);
