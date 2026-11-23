<?php

declare(strict_types=1);
return [
    'tx_cart_domain_model_order_item' => [
        [
            'uid' => 1,
            'pid' => 1,
            'currency' => '€',
            'gross' => 34.50,
            'net' => 30.00,
            'total_gross' => 29.74,
            'total_net' => 26.00,
        ],
    ],
    'tx_cart_domain_model_order_taxclass' => [
        ['uid' => 1, 'pid' => 1, 'item' => 1, 'title' => 'normal', 'value' => '19', 'calc' => 0.19],
        ['uid' => 2, 'pid' => 1, 'item' => 1, 'title' => 'reduced', 'value' => '7', 'calc' => 0.07],
    ],
    'tx_cart_domain_model_order_product' => [
        [
            'uid' => 1,
            'pid' => 1,
            'item' => 1,
            'sku' => 'book',
            'title' => 'Book',
            'count' => 1,
            'price' => 10.70,
            'gross' => 10.70,
            'net' => 10.00,
            'tax' => 0.70,
            'tax_class' => 2,
        ],
        [
            'uid' => 2,
            'pid' => 1,
            'item' => 1,
            'sku' => 'shirt',
            'title' => 'Shirt',
            'count' => 1,
            'price' => 23.80,
            'gross' => 23.80,
            'net' => 20.00,
            'tax' => 3.80,
            'tax_class' => 1,
        ],
    ],
    'tx_cart_domain_model_order_shipping' => [
        [
            'uid' => 1,
            'pid' => 1,
            'item' => 1,
            'name' => 'Standard',
            'gross' => 5.95,
            'net' => 5.00,
            'tax' => 0.95,
            'tax_class' => 1,
        ],
    ],
    'tx_cart_domain_model_order_payment' => [
        [
            'uid' => 1,
            'pid' => 1,
            'item' => 1,
            'name' => 'Invoice',
            'gross' => 1.19,
            'net' => 1.00,
            'tax' => 0.19,
            'tax_class' => 1,
        ],
    ],
    'tx_cart_domain_model_order_discount' => [
        [
            'uid' => 1,
            'pid' => 1,
            'item' => 1,
            'title' => 'Coupon',
            'code' => 'coupon',
            'gross' => -11.90,
            'net' => -10.00,
            'tax_class_id' => 1,
            'tax' => -1.90,
        ],
    ],
    'tx_cart_domain_model_order_tax' => [
        ['uid' => 1, 'pid' => 1, 'item' => 1, 'record_type' => 'tax', 'tax' => 0.70, 'tax_class' => 2],
        ['uid' => 2, 'pid' => 1, 'item' => 1, 'record_type' => 'tax', 'tax' => 3.80, 'tax_class' => 1],
        ['uid' => 3, 'pid' => 1, 'item' => 1, 'record_type' => 'total_tax', 'tax' => 3.04, 'tax_class' => 1],
        ['uid' => 4, 'pid' => 1, 'item' => 1, 'record_type' => 'total_tax', 'tax' => 0.70, 'tax_class' => 2],
    ],
];
