<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Backend\Widgets\Provider;

use Extcode\Cart\Domain\Order\Status\PaymentStatus;
use Extcode\Cart\Domain\Order\Status\ShippingStatus;
use Extcode\Cart\Widgets\Provider\OrdersPerDayProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('extcode.cart.provider.orders_per_day')
        ->class(OrdersPerDayProvider::class)
        ->autowire()
        ->arg('$options', [
            'filter' => [
                'payment' => [
                    'status' => PaymentStatus::PAID,
                ],
                'shipping' => [
                    'status' => ShippingStatus::OPEN,
                ],
            ],
        ])
    ;
};
