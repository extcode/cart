<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Backend\Widgets;

use Extcode\Cart\Widgets\Access\StoragePidAccess;
use Extcode\Cart\Widgets\Query\OrderItemQuery;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Dashboard\Widgets\BarChartWidget;

/*
 * Services of the dashboard widgets, only registered if typo3/cms-dashboard
 * is installed.
 */
return static function (ContainerConfigurator $configurator, ContainerBuilder $containerBuilder): void {
    if (!$containerBuilder->hasDefinition(BarChartWidget::class)) {
        return;
    }

    $services = $configurator->services()
        ->defaults()
        ->autowire()
    ;

    $services->set(OrderItemQuery::class)
        ->arg('$queryBuilder', new Reference('querybuilder.tx_cart_domain_model_order_item'))
    ;

    $services->set(StoragePidAccess::class);

    $configurator->import('Provider/PaymentPaidShippingOpenProvider.php');
    $configurator->import('PaymentPaidShippingOpenWidget.php');

    $configurator->import('Provider/OrdersPerDayProvider.php');
    $configurator->import('OrdersPerDayWidget.php');

    $configurator->import('Provider/TurnoverPerDayProvider.php');
    $configurator->import('TurnoverPerDayWidget.php');
};
