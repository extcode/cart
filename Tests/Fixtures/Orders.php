<?php

declare(strict_types=1);

use Extcode\Cart\Domain\Model\Order\BillingAddress;

return [
    'tx_cart_domain_model_order_item' => [
        0 => [
            'uid' => 10,
            'pid' => 105,
            'billing_address' => 1,
        ],
        1 => [
            'uid' => 11,
            'pid' => 105,
            'billing_address' => 1,
            'order_number' => 'O-20260121-7',
            'order_date' => 1769034161,
            'invoice_number' => 'I-20260122-3',
            'invoice_date' => 1769120561,
        ],
        2 => [
            'uid' => 12,
            'pid' => 105,
            'billing_address' => 0,
            'order_number' => 'O-20260121-7',
            'order_date' => 1769034161,
            'invoice_number' => 'I-20260122-3',
            'invoice_date' => 1769120561,
        ],
    ],
    'tx_cart_domain_model_order_address' => [
        0 => [
            'uid' => 20,
            'pid' => 105,
            'item' => 10,
            'record_type' => '\\' . BillingAddress::class,
        ],
        1 => [
            'uid' => 21,
            'pid' => 105,
            'item' => 11,
            'record_type' => '\\' . BillingAddress::class,
            'salutation' => 'Mr',
            'title' => '',
            'first_name' => 'Arthur',
            'last_name' => 'Dent',
        ],
    ],
];
