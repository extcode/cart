<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Backend\Provider;

use Extcode\Cart\Widgets\Provider\OrderItemsProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set('extcode.cart.provider.payment_paid_shipping_open')
        ->class(OrderItemsProvider::class)
        ->arg('$queryBuilder', new Reference('querybuilder.tx_cart_domain_model_order_item'))
        ->arg('$options', [
            'filter' => [
                'payment' => [
                    'status' => 'paid',
                ],
                'shipping' => [
                    'status' => 'open',
                ],
            ],
        ]);
};
