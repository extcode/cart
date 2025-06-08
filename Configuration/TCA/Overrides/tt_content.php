<?php

defined('TYPO3') or die();

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

call_user_func(function () {
    $_LLL_db = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf:';

    $pluginNames = [
        'MiniCart' => [
            'iconIdentifier' => 'ext-cart-wizard-icon',
        ],
        'Cart' => [
            'iconIdentifier' => 'ext-cart-wizard-icon',
        ],
        'Currency' => [
            'iconIdentifier' => 'ext-cart-wizard-icon',
        ],
        'Order' => [
            'iconIdentifier' => 'ext-cart-wizard-icon',
        ],
    ];

    foreach ($pluginNames as $pluginName => $pluginConfig) {
        $pluginSignature = 'cart_' . strtolower($pluginName);

        $contentTypeName = ExtensionUtility::registerPlugin(
            'Cart',
            $pluginName,
            $_LLL_db . 'tx_cart.plugin.' . $pluginSignature,
            $pluginConfig['iconIdentifier'],
            'cart'
        );

        $flexFormPath = 'EXT:cart/Configuration/FlexForms/' . $pluginName . 'Plugin.xml';
        
        if (file_exists(GeneralUtility::getFileAbsFileName($flexFormPath))) {

            ExtensionManagementUtility::addPiFlexFormValue(
                '*',
                'FILE:' . $flexFormPath,
                $contentTypeName
            );

            ExtensionManagementUtility::addToAllTCAtypes(
                'tt_content',
                '--div--;Configuration,pi_flexform,',
                $contentTypeName,
                'after:subheader',
            );
            
        }
    }
});
