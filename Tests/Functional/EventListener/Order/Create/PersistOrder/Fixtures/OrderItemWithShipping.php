<?php

declare(strict_types=1);

return [
    'tx_cart_domain_model_order_item' => [
        ['uid' => 1, 'pid' => 105, 'currency' => '€', 'products' => 2, 'shipping' => 1],
    ],
    'tx_cart_domain_model_order_shipping' => [
        ['uid' => 1, 'pid' => 105, 'item' => 1, 'name' => 'Standard', 'status' => 'open'],
    ],
    'tx_cart_domain_model_order_product' => [
        ['uid' => 1, 'pid' => 105, 'item' => 1, 'sku' => 'ticket', 'count' => 2, 'shippable' => 0],
        ['uid' => 2, 'pid' => 105, 'item' => 1, 'sku' => 'download', 'count' => 1, 'shippable' => 0],
    ],
];
