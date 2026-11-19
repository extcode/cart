<?php

declare(strict_types=1);

use Extcode\Cart\Domain\Model\Order\Status\PaymentStatus;

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
