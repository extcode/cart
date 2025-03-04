<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration;

use Extcode\Cart\Hooks\ItemsProcFunc;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManager;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManager;

return function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder) {
    if ($containerBuilder->hasDefinition(BarChartWidget::class)) {
        $containerConfigurator->import('Backend/Provider/PaymentPaidShippingOpenProvider.php');
        $containerConfigurator->import('Backend/Widgets/PaymentPaidShippingOpenWidget.php');

        $containerConfigurator->import('Backend/Provider/OrdersPerDayProvider.php');
        $containerConfigurator->import('Backend/Widgets/OrdersPerDayWidget.php');

        $containerConfigurator->import('Backend/Provider/TurnoverPerDayProvider.php');
        $containerConfigurator->import('Backend/Widgets/TurnoverPerDayWidget.php');
    }

    if (
        $containerBuilder->hasDefinition(ConfigurationManager::class)
        && $containerBuilder->hasDefinition(FormPersistenceManager::class)
    ) {
        $services = $containerConfigurator->services();

        $services->set(ItemsProcFunc::class)
            ->public();
    }

    $containerConfigurator->import('Services/ConsoleCommands.php');
};
