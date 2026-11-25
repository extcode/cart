<?php

declare(strict_types=1);

return [
    'tx_cart_domain_model_order_item' => [
        ['uid' => 1, 'pid' => 105, 'order_number' => 'O-1', 'products' => 3, 'shipping' => 1],
        ['uid' => 2, 'pid' => 105, 'order_number' => 'O-2', 'products' => 1, 'shipping' => 1],
    ],
    'tx_cart_domain_model_order_shipping' => [
        ['uid' => 1, 'pid' => 105, 'item' => 1, 'name' => 'Standard', 'status' => 'open'],
        ['uid' => 2, 'pid' => 105, 'item' => 2, 'name' => 'Standard', 'status' => 'open'],
    ],
    'tx_cart_domain_model_order_product' => [
        ['uid' => 11, 'pid' => 105, 'item' => 1, 'sku' => 'book', 'count' => 3, 'shippable' => 1],
        ['uid' => 12, 'pid' => 105, 'item' => 1, 'sku' => 'mug', 'count' => 1, 'shippable' => 1],
        ['uid' => 13, 'pid' => 105, 'item' => 1, 'sku' => 'download', 'count' => 1, 'shippable' => 0],
        ['uid' => 21, 'pid' => 105, 'item' => 2, 'sku' => 'book', 'count' => 1, 'shippable' => 1],
    ],
];
