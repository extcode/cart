<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration;

use Extcode\Cart\Domain\Order\Fulfillment\OrderProductQuantityCalculator;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentCreationService;
use Extcode\Cart\Domain\Order\Fulfillment\ShipmentStatusChangeService;
use Extcode\Cart\Domain\Order\History\OrderChangeRepository;
use Extcode\Cart\Domain\Order\History\OrderHistoryService;
use Extcode\Cart\Domain\Order\Status\Mail\StatusChangeMailRuleLoader;
use Extcode\Cart\Domain\Order\Status\OrderStatusConfigurationBuilder;
use Extcode\Cart\Domain\Order\Status\OrderStatusRegistry;
use Extcode\Cart\Domain\Order\Status\StatusChangeService;
use Extcode\Cart\Hooks\FormDefinitions;
use Extcode\Cart\Service\CurrencyTranslationService;
use Extcode\Cart\Service\CurrencyTranslationServiceInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Form\Mvc\Configuration\ConfigurationManager;
use TYPO3\CMS\Form\Mvc\Persistence\FormPersistenceManager;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator, ContainerBuilder $containerBuilder): void {
    $containerConfigurator->import('Backend/Widgets/Services.php');

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

    $services->set(OrderProductQuantityCalculator::class)->public();
    $services->set(ShipmentCreationService::class)->public();
    $services->set(ShipmentStatusChangeService::class)->public();

    // @deprecated since cart 12.7, will be removed in cart 13.0
    $deprecatedServiceIds = [
        'Extcode\\Cart\\OrderHistory\\OrderChangeRepository' => OrderChangeRepository::class,
        'Extcode\\Cart\\OrderHistory\\OrderHistoryService' => OrderHistoryService::class,
        'Extcode\\Cart\\OrderStatus\\Mail\\StatusChangeMailRuleLoader' => StatusChangeMailRuleLoader::class,
        'Extcode\\Cart\\OrderStatus\\OrderStatusConfigurationBuilder' => OrderStatusConfigurationBuilder::class,
        'Extcode\\Cart\\OrderStatus\\OrderStatusRegistry' => OrderStatusRegistry::class,
        'Extcode\\Cart\\OrderStatus\\StatusChangeService' => StatusChangeService::class,
    ];
    foreach ($deprecatedServiceIds as $deprecatedServiceId => $serviceId) {
        $services
            ->alias(
                $deprecatedServiceId,
                $serviceId
            )
            ->public()
            ->deprecate(
                'extcode/cart',
                '12.7',
                'The service "%alias_id%" is deprecated, use "' . $serviceId . '" instead.'
            )
        ;
    }

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
        $services->set(FormDefinitions::class)
            ->public()
        ;
    }

    $containerConfigurator->import('Services/ConfigurationLoader.php');
    $containerConfigurator->import('Services/ConsoleCommands.php');
    $containerConfigurator->import('Services/EventListeners.php');
};
