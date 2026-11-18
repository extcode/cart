<?php

declare(strict_types=1);

return [
    'be_dashboards' => [
        [
            'uid' => 1,
            'pid' => 0,
            'identifier' => 'cart',
            'title' => 'Cart',
            'cruser_id' => 1,
            'widgets' => json_encode([
                'orders-per-day' => [
                    'identifier' => 'OrdersPerDay',
                ],
                'turnover-per-day' => [
                    'identifier' => 'TurnoverPerDay',
                ],
                'payment-paid-shipping-open' => [
                    'identifier' => 'PaymentPaidShippingOpen',
                ],
                'orders-today' => [
                    'identifier' => 'OrdersToday',
                ],
                'turnover-today' => [
                    'identifier' => 'TurnoverToday',
                ],
            ], JSON_THROW_ON_ERROR),
        ],
    ],
];
