<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3_MODE') or die();

call_user_func(function () {
    ExtensionManagementUtility::addStaticFile(
        'cart',
        'Configuration/TypoScript',
        'Shopping Cart - Cart'
    );
});
