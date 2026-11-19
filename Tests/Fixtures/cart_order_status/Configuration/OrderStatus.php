<?php

declare(strict_types=1);

use Extcode\Cart\Domain\Model\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Model\Order\Status\ShippingStatus;

$_LLL = 'LLL:EXT:cart_order_status/Resources/Private/Language/locallang.xlf';

return [
    'payment' => [
        'new' => [
            'label' => $_LLL . ':payment.status.new',
            'before' => [PaymentStatus::OPEN],
        ],
        'refunded' => [
            'label' => $_LLL . ':payment.status.refunded',
            'after' => [PaymentStatus::PAID],
            'before' => [PaymentStatus::CANCELED],
        ],
        'chargeback' => [
            'label' => $_LLL . ':payment.status.chargeback',
            'after' => [PaymentStatus::CANCELED],
        ],
    ],
    'shipping' => [
        'ordered' => [
            'label' => $_LLL . ':shipping.status.ordered',
            'before' => [ShippingStatus::OPEN],
        ],
        'packed' => [
            'label' => $_LLL . ':shipping.status.packed',
            'after' => [ShippingStatus::IN_PROCESS],
            'before' => [ShippingStatus::SHIPPED],
        ],
        'delivered' => [
            'label' => $_LLL . ':shipping.status.delivered',
            'after' => [ShippingStatus::SHIPPED],
        ],
    ],
];
