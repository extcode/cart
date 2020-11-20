<?php
declare(strict_types=1);
namespace Extcode\Cart\Configuration\Backend\Widget;

use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set('dashboard.widget.extcode.cart.orders_per_day')
        ->class(\TYPO3\CMS\Dashboard\Widgets\BarChartWidget::class)
        ->arg('$dataProvider', new Reference('extcode.cart.provider.orders_per_day'))
        ->arg('$view', new Reference('dashboard.views.widget'))
        ->tag('dashboard.widget', [
            'identifier' => 'OrdersPerDay',
            'groupNames' => 'cart',
            'title' => 'LLL:EXT:cart/Resources/Private/Language/locallang_be.xlf:dashboard.widgets.orders_per_day.title',
            'description' => 'LLL:EXT:cart/Resources/Private/Language/locallang_be.xlf:dashboard.widgets.orders_per_day.description',
            'iconIdentifier' => 'content-widget-chart-bar',
            'additionalCssClasses' => 'dashboard-item--chart',
            'height' => 'medium',
            'width' => 'medium'
        ]);
};
