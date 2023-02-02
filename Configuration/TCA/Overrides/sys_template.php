<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

call_user_func(function () {
    ExtensionManagementUtility::addStaticFile(
        'cart',
        'Configuration/TypoScript',
        'Shopping Cart - Cart'
    );
});
