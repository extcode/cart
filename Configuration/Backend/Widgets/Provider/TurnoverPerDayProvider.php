<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Backend\Widgets\Provider;

use Extcode\Cart\Widgets\Provider\TurnoverPerDayProvider;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $services->set('extcode.cart.provider.turnover_per_day')
        ->class(TurnoverPerDayProvider::class)
        ->autowire()
        ->arg('$options', [
            'sum' => 'total_gross',
        ])
    ;
};
