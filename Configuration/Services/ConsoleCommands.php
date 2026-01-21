<?php

declare(strict_types=1);

use Extcode\Cart\Command\OrderItemCleanupCommand;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator) {
    $services = $containerConfigurator
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->set(OrderItemCleanupCommand::class)
        ->tag(
            'console.command',
            [
                'command' => 'order:cleanup',
            ]
        )
    ;
};
