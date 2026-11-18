<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Backend\Widgets;

use Extcode\Cart\Widgets\NumberWidget;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Backend\View\BackendViewFactory;

return static function (ContainerConfigurator $configurator): void {
    $services = $configurator->services();

    $widgets = [
        'OrdersToday' => 'orders_today',
        'OrdersThisMonth' => 'orders_this_month',
    ];

    foreach ($widgets as $identifier => $key) {
        $title = 'LLL:EXT:cart/Resources/Private/Language/locallang_be.xlf:dashboard.widgets.' . $key . '.title';

        $services->set('dashboard.widget.extcode.cart.' . $key)
            ->class(NumberWidget::class)
            ->arg('$backendViewFactory', new Reference(BackendViewFactory::class))
            ->arg('$dataProvider', new Reference('extcode.cart.provider.' . $key))
            ->arg('$options', [
                'icon' => 'actions-cart',
            ])
            ->tag('dashboard.widget', [
                'identifier' => $identifier,
                'groupNames' => 'cart',
                'title' => $title,
                'description' => 'LLL:EXT:cart/Resources/Private/Language/locallang_be.xlf:dashboard.widgets.' . $key . '.description',
                'iconIdentifier' => 'content-widget-number',
            ])
        ;
    }
};
