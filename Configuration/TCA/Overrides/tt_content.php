<?php

defined('TYPO3') or die();

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

        $flexFormPath = 'EXT:cart/Configuration/FlexForms/' . $pluginName . 'Plugin.xml';
        if (file_exists(GeneralUtility::getFileAbsFileName($flexFormPath))) {
            $flexFormPath = 'FILE:' . $flexFormPath;
        } else {
            $flexFormPath = '';
        }

        ExtensionUtility::registerPlugin(
            'Cart',
            $pluginName,
            $_LLL_db . 'tx_cart.plugin.' . $pluginSignature,
            $pluginConfig['iconIdentifier'],
            'cart',
            $_LLL_db . 'tx_cart.plugin.' . $pluginSignature . '.description',
            $flexFormPath
        );
    }
});
