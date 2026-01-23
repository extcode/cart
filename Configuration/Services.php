<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder) {
    if ($containerBuilder->hasDefinition('TYPO3\CMS\Dashboard\Widgets\BarChartWidget')) {
        $containerConfigurator->import('Backend/Provider/PaymentPaidShippingOpenProvider.php');
        $containerConfigurator->import('Backend/Widgets/PaymentPaidShippingOpenWidget.php');

        $containerConfigurator->import('Backend/Provider/OrdersPerDayProvider.php');
        $containerConfigurator->import('Backend/Widgets/OrdersPerDayWidget.php');

        $containerConfigurator->import('Backend/Provider/TurnoverPerDayProvider.php');
        $containerConfigurator->import('Backend/Widgets/TurnoverPerDayWidget.php');
    }

    if (
        $containerBuilder->hasDefinition('TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManager')
        && $containerBuilder->hasDefinition('TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManager')
    ) {
        $services = $containerConfigurator->services();

        $services->set('Extcode\Cart\Hooks\ItemsProcFunc')
            ->public();
    }

    $containerConfigurator->import('Services/ConsoleCommands.php');
};
