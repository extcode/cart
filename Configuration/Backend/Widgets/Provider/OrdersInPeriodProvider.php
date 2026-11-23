<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Backend\Widgets\Provider;

use Extcode\Cart\Domain\Order\Status\PaymentStatus;
use Extcode\Cart\Widgets\Provider\OrdersInPeriodProvider;
use Extcode\Cart\Widgets\Query\Period;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('extcode.cart.provider.orders_today')
        ->class(OrdersInPeriodProvider::class)
        ->autowire()
        ->arg('$options', [
            'period' => Period::TODAY,
            'filter' => [
                'payment' => [
                    'status' => [PaymentStatus::OPEN, PaymentStatus::PENDING, PaymentStatus::PAID],
                ],
            ],
        ])
    ;

    $services->set('extcode.cart.provider.orders_this_month')
        ->class(OrdersInPeriodProvider::class)
        ->autowire()
        ->arg('$options', [
            'period' => Period::MONTH,
            'filter' => [
                'payment' => [
                    'status' => [PaymentStatus::OPEN, PaymentStatus::PENDING, PaymentStatus::PAID],
                ],
            ],
        ])
    ;
};
