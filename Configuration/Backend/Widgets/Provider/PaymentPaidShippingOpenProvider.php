<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Backend\Widgets\Provider;

use Extcode\Cart\Widgets\Provider\OrderItemsProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('extcode.cart.provider.payment_paid_shipping_open')
        ->class(OrderItemsProvider::class)
        ->autowire()
        ->arg('$options', [
            'filter' => [
                'payment' => [
                    'status' => 'paid',
                ],
                'shipping' => [
                    'status' => 'open',
                ],
            ],
        ])
    ;
};
