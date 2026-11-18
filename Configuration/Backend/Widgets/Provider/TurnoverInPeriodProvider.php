<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Backend\Widgets\Provider;

use Extcode\Cart\Widgets\Provider\TurnoverInPeriodProvider;
use Extcode\Cart\Widgets\Query\Period;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('extcode.cart.provider.turnover_today')
        ->class(TurnoverInPeriodProvider::class)
        ->autowire()
        ->arg('$options', [
            'period' => Period::TODAY,
            'filter' => [
                'payment' => [
                    'status' => ['open', 'pending', 'paid'],
                ],
            ],
            'sum' => 'total_gross',
        ])
    ;

    $services->set('extcode.cart.provider.turnover_this_month')
        ->class(TurnoverInPeriodProvider::class)
        ->autowire()
        ->arg('$options', [
            'period' => Period::MONTH,
            'filter' => [
                'payment' => [
                    'status' => ['open', 'pending', 'paid'],
                ],
            ],
            'sum' => 'total_gross',
        ])
    ;
};
