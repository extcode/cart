<?php

defined('TYPO3_MODE') or die();

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'cart',
    'tx_cart_domain_model_product_product',
    'main_category',
    [
        'label' => $_LLL . ':tx_cart_domain_model_product_product.main_category',
        'fieldConfiguration' => [
            'minitems' => 0,
            'maxitems' => 1,
            'multiple' => false,
        ]
    ]
);

$GLOBALS['TCA']['tx_cart_domain_model_product_product']['main_category']['config']['maxitems'] = 1;

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::makeCategorizable(
    'cart',
    'tx_cart_domain_model_product_product',
    'categories',
    [
        'label' => $_LLL . ':tx_cart_domain_model_product_product.categories'
    ]
);
