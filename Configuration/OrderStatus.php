<?php

declare(strict_types=1);

use Extcode\Cart\Domain\Model\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Model\Order\Status\ShippingStatus;

$_LLL = 'LLL:EXT:cart/Resources/Private/Language/locallang_db.xlf';

return [
    'payment' => [
        PaymentStatus::OPEN => [
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.status.open',
        ],
        PaymentStatus::PENDING => [
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.status.pending',
            'after' => [PaymentStatus::OPEN],
        ],
        PaymentStatus::PAID => [
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.status.paid',
            'after' => [PaymentStatus::PENDING],
        ],
        PaymentStatus::CANCELED => [
            'label' => $_LLL . ':tx_cart_domain_model_order_payment.status.canceled',
            'after' => [PaymentStatus::PAID],
        ],
    ],
    'shipping' => [
        ShippingStatus::OPEN => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.status.open',
        ],
        ShippingStatus::ON_HOLD => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.status.on_hold',
            'after' => [ShippingStatus::OPEN],
        ],
        ShippingStatus::IN_PROCESS => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.status.in_process',
            'after' => [ShippingStatus::ON_HOLD],
        ],
        ShippingStatus::SHIPPED => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.status.shipped',
            'after' => [ShippingStatus::IN_PROCESS],
        ],
    ],
];
