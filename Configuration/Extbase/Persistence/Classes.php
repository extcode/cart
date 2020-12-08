<?php
declare(strict_types = 1);

return [
    \Extcode\Cart\Domain\Model\Order\BillingAddress::class => [
        'tableName' => 'tx_cart_domain_model_order_address',
        'recordType' => '\\' . \Extcode\Cart\Domain\Model\Order\BillingAddress::class,
    ],
    \Extcode\Cart\Domain\Model\Order\ShippingAddress::class => [
        'tableName' => 'tx_cart_domain_model_order_address',
        'recordType' => '\\' . \Extcode\Cart\Domain\Model\Order\ShippingAddress::class,
    ],
];
