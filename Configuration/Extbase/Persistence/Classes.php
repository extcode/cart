<?php
declare(strict_types = 1);

use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;
use TYPO3\CMS\Extbase\Domain\Model\FrontendUser;

return [
    BillingAddress::class => [
        'tableName' => 'tx_cart_domain_model_order_address',
        'recordType' => '\\' . BillingAddress::class,
    ],
    FrontendUser::class => [
        'tableName' => 'fe_users',
        'properties' => [
            'lockToDomain' => [
                'fieldName' => 'lockToDomain'
            ]
        ],
    ],
    ShippingAddress::class => [
        'tableName' => 'tx_cart_domain_model_order_address',
        'recordType' => '\\' . ShippingAddress::class,
    ],
];
