<?php
declare(strict_types = 1);

use Extcode\Cart\Domain\Model\Order\BillingAddress;
use Extcode\Cart\Domain\Model\Order\ShippingAddress;

return [
    BillingAddress::class => [
        'tableName' => 'tx_cart_domain_model_order_address',
        'recordType' => '\\' . BillingAddress::class,
    ],
    ShippingAddress::class => [
        'tableName' => 'tx_cart_domain_model_order_address',
        'recordType' => '\\' . ShippingAddress::class,
    ],
];
