<?php

declare(strict_types=1);

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

defined('TYPO3') or die();

ExtensionManagementUtility::addTcaSelectItem(
    'tx_cart_domain_model_order_shipping',
    'status',
    [
        'label' => 'Delivered via TCA',
        'value' => 'delivered_via_tca',
    ]
);
