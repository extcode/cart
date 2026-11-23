<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Backend\Widgets\Provider;

use Extcode\Cart\Domain\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;
use Extcode\Cart\Widgets\Provider\OrderItemsProvider;
use Extcode\Cart\Widgets\Provider\OrderListButtonProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $filter = [
        'payment' => [
            'status' => PaymentStatus::PAID,
        ],
        'shipping' => [
            'status' => ShippingStatus::OPEN,
        ],
    ];

    $services->set('extcode.cart.provider.payment_paid_shipping_open')
        ->class(OrderItemsProvider::class)
        ->autowire()
        ->arg('$options', [
            'filter' => $filter,
        ])
    ;

    $services->set('extcode.cart.button.payment_paid_shipping_open')
        ->class(OrderListButtonProvider::class)
        ->autowire()
        ->arg('$options', [
            'filter' => $filter,
        ])
    ;
};
