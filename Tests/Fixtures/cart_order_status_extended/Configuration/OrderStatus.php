<?php

declare(strict_types=1);

use Extcode\Cart\OrderStatus\PaymentStatus;

return [
    'payment' => [
        'partially_refunded' => [
            'label' => 'Partially refunded',
            'after' => [PaymentStatus::PAID],
            'before' => ['refunded'],
        ],
        PaymentStatus::PENDING => [
            'label' => 'Awaiting payment',
        ],
    ],
];
