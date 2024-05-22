<?php

declare(strict_types=1);

namespace Extcode\Cart\Configuration\Backend\Widget;

use Extcode\Cart\Widgets\PaymentPaidShippingOpen;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symfony\Component\DependencyInjection\Reference;
use TYPO3\CMS\Backend\View\BackendViewFactory;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->set('dashboard.widget.extcode.cart.payment_paid_shipping_open')
        ->class(PaymentPaidShippingOpen::class)
        ->arg('$backendViewFactory', new Reference(BackendViewFactory::class))
        ->arg('$dataProvider', new Reference('extcode.cart.provider.payment_paid_shipping_open'))
        ->tag('dashboard.widget', [
            'identifier' => 'PaymentPaidShippingOpen',
            'groupNames' => 'cart',
            'title' => 'LLL:EXT:cart/Resources/Private/Language/locallang_be.xlf:dashboard.widgets.payment_paid_shipping_open.title',
            'description' => 'LLL:EXT:cart/Resources/Private/Language/locallang_be.xlf:dashboard.widgets.payment_paid_shipping_open.description',
            'iconIdentifier' => 'content-widget-list',
            'height' => 'large',
            'width' => 'medium',
        ]);
};
