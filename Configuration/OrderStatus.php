<?php

declare(strict_types=1);

use Extcode\Cart\Domain\Order\Fulfillment\ShipmentStatus;
use Extcode\Cart\Domain\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;

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
        ShippingStatus::PARTIALLY_SHIPPED => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.status.partially_shipped',
            'after' => [ShippingStatus::IN_PROCESS],
            'before' => [ShippingStatus::SHIPPED],
        ],
        ShippingStatus::SHIPPED => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.status.shipped',
            'after' => [ShippingStatus::IN_PROCESS],
        ],
        ShippingStatus::NOT_REQUIRED => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipping.status.not_required',
            'after' => [ShippingStatus::SHIPPED],
        ],
    ],
    'shipment' => [
        ShipmentStatus::OPEN => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.status.open',
        ],
        ShipmentStatus::IN_PROCESS => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.status.in_process',
            'after' => [ShipmentStatus::OPEN],
        ],
        ShipmentStatus::SHIPPED => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.status.shipped',
            'after' => [ShipmentStatus::IN_PROCESS],
        ],
        ShipmentStatus::DELIVERED => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.status.delivered',
            'after' => [ShipmentStatus::SHIPPED],
        ],
        ShipmentStatus::CANCELED => [
            'label' => $_LLL . ':tx_cart_domain_model_order_shipment.status.canceled',
            'after' => [ShipmentStatus::DELIVERED],
        ],
    ],
];
