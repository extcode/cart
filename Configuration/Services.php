<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration;

use Extcode\Cart\Hooks\ItemsProcFunc;
use Extcode\Cart\Service\CurrencyTranslationService;
use Extcode\Cart\Service\CurrencyTranslationServiceInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManager;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManager;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    if ($containerBuilder->hasDefinition(BarChartWidget::class)) {
        $containerConfigurator->import('Backend/Provider/PaymentPaidShippingOpenProvider.php');
        $containerConfigurator->import('Backend/Widgets/PaymentPaidShippingOpenWidget.php');

        $containerConfigurator->import('Backend/Provider/OrdersPerDayProvider.php');
        $containerConfigurator->import('Backend/Widgets/OrdersPerDayWidget.php');

        $containerConfigurator->import('Backend/Provider/TurnoverPerDayProvider.php');
        $containerConfigurator->import('Backend/Widgets/TurnoverPerDayWidget.php');
    }

    $services = $containerConfigurator
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->load(
            'Extcode\\Cart\\',
            '../Classes/*'
        )
        ->exclude(
            [
                '../Classes/Widgets/*',
                '../Classes/Command/*',
            ]
        )
    ;

    $services
        ->alias(
            CurrencyTranslationServiceInterface::class,
            CurrencyTranslationService::class
        )
        ->public()
    ;

    $services
        ->set(
            'querybuilder.tx_cart_domain_model_order_item',
            QueryBuilder::class
        )
        ->factory(
            [
                service(ConnectionPool::class),
                'getQueryBuilderForTable',
            ]
        )
        ->arg('$tableName', 'tx_cart_domain_model_order_item')
    ;

    if (
        $containerBuilder->hasDefinition(ConfigurationManager::class)
        && $containerBuilder->hasDefinition(FormPersistenceManager::class)
    ) {
        $services->set(ItemsProcFunc::class)
            ->public()
        ;
    }

    $containerConfigurator->import('Services/ConfigurationLoader.php');
    $containerConfigurator->import('Services/ConsoleCommands.php');
    $containerConfigurator->import('Services/EventListeners.php');
};
