<?php

declare(strict_types=1);

use Extcode\Cart\Domain\Model\FrontendUser;
use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;

return [
    BillingAddress::class => [
        'tableName' => 'tx_cart_domain_model_order_address',
        'recordType' => '\\' . BillingAddress::class,
    ],
    FrontendUser::class => [
        'tableName' => 'fe_users',
        'properties' => [
            'lockToDomain' => [
                'fieldName' => 'lockToDomain',
            ],
        ],
    ],
    ShippingAddress::class => [
        'tableName' => 'tx_cart_domain_model_order_address',
        'recordType' => '\\' . ShippingAddress::class,
    ],
];
