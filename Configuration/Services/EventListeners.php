<?php

declare(strict_types=1);

use Extcode\Cart\Event\Cart\UpdateCountryEvent;
use Extcode\Cart\Event\Cart\UpdateCurrencyEvent;
use Extcode\Cart\Event\Order\CreateEvent;
use Extcode\Cart\Event\Order\FinishEvent;
use Extcode\Cart\Event\Order\NumberGeneratorEvent;
use Extcode\Cart\Event\Order\PersistOrderEvent;
use Extcode\Cart\Event\Order\UpdateServiceEvent;
use Extcode\Cart\Event\Template\Components\ModifyButtonBarEvent;
use Extcode\Cart\EventListener\Cart\UpdateCountry;
use Extcode\Cart\EventListener\Cart\UpdateCurrency;
use Extcode\Cart\EventListener\Mail\AttachmentFromOrderItem;
use Extcode\Cart\EventListener\Mail\AttachmentFromTypoScript;
use Extcode\Cart\EventListener\Order\Create\DeliveryNumber;
use Extcode\Cart\EventListener\Order\Create\InvoiceNumber;
use Extcode\Cart\EventListener\Order\Create\Order;
use Extcode\Cart\EventListener\Order\Create\OrderNumber;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\Coupons;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\Item;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\Payment;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\Products;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\Shipping;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\TaxClasses;
use Extcode\Cart\EventListener\Order\Create\PersistOrder\Taxes;
use Extcode\Cart\EventListener\Order\Finish\ClearCart;
use Extcode\Cart\EventListener\Order\Finish\Email;
use Extcode\Cart\EventListener\Order\Update\LogServiceUpdate;
use Extcode\Cart\EventListener\Template\Components\ModifyButtonBar;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use TYPO3\CMS\Extbase\Persistence\Generic\PersistenceManager;

use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator
        ->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
    ;

    $services
        ->set(ModifyButtonBar::class)
        ->tag(
            'event.listener',
            [
                'event' => ModifyButtonBarEvent::class,
                'identifier' => 'cart--cart--template--components--modify-button-bar',
            ]
        )
    ;

    $services
        ->set(UpdateCountry::class)
        ->tag(
            'event.listener',
            [
                'event' => UpdateCountryEvent::class,
                'identifier' => 'cart--cart--update-country',
            ]
        )
    ;

    $services
        ->set(UpdateCurrency::class)
        ->tag(
            'event.listener',
            [
                'event' => UpdateCurrencyEvent::class,
                'identifier' => 'cart--cart--update-currency',
            ]
        )
    ;

    $services
        ->set(AttachmentFromOrderItem::class)
        ->tag(
            'event.listener',
            [
                'identifier' => 'cart--mail--attachment-from-order-item',
            ]
        )
    ;

    $services
        ->set(AttachmentFromTypoScript::class)
        ->tag(
            'event.listener',
            [
                'identifier' => 'cart--mail--attachment-from-typoscript',
            ]
        )
    ;

    $services
        ->set(Order::class)
        ->tag(
            'event.listener',
            [
                'event' => CreateEvent::class,
                'identifier' => 'cart--order--create--order',
            ]
        )
    ;

    $services
        ->set(Item::class)
        ->tag(
            'event.listener',
            [
                'event' => PersistOrderEvent::class,
                'identifier' => 'cart--order--create--persist-order--item',
            ]
        )
    ;

    $services
        ->set(TaxClasses::class)
        ->tag(
            'event.listener',
            [
                'event' => PersistOrderEvent::class,
                'identifier' => 'cart--order--create--persist-order--tax-classes',
                'after' => 'cart--order--create--persist-order--item',
            ]
        )
    ;

    $services
        ->set(Taxes::class)
        ->tag(
            'event.listener',
            [
                'event' => PersistOrderEvent::class,
                'identifier' => 'cart--order--create--persist-order--taxes',
                'after' => 'cart--order--create--persist-order--tax-classes',
            ]
        )
    ;

    $services
        ->set(Products::class)
        ->tag(
            'event.listener',
            [
                'event' => PersistOrderEvent::class,
                'identifier' => 'cart--order--create--persist-order--products',
                'after' => 'cart--order--create--persist-order--tax-classes',
            ]
        )
    ;

    $services
        ->set(Coupons::class)
        ->tag(
            'event.listener',
            [
                'event' => PersistOrderEvent::class,
                'identifier' => 'cart--order--create--persist-order--coupons',
                'after' => 'cart--order--create--persist-order--tax-classes',
            ]
        )
    ;

    $services
        ->set(Payment::class)
        ->tag(
            'event.listener',
            [
                'event' => PersistOrderEvent::class,
                'identifier' => 'cart--order--create--persist-order--payment',
                'after' => 'cart--order--create--persist-order--tax-classes',
            ]
        )
    ;

    $services
        ->set(Shipping::class)
        ->tag(
            'event.listener',
            [
                'event' => PersistOrderEvent::class,
                'identifier' => 'cart--order--create--persist-order--shipping',
                'after' => 'cart--order--create--persist-order--tax-classes',
            ]
        )
    ;

    $services
        ->set(OrderNumber::class)
        ->tag(
            'event.listener',
            [
                'event' => NumberGeneratorEvent::class,
                'identifier' => 'cart--order--create--order-number',
                'after' => 'cart--order--create--order',
            ]
        )
        ->arg(
            '$persistenceManager',
            service(PersistenceManager::class)
        )
    ;

    $services
        ->set(InvoiceNumber::class)
        ->tag(
            'event.listener',
            [
                'event' => NumberGeneratorEvent::class,
                'identifier' => 'cart--order--create--invoice-number',
                'after' => 'cart--order--create--order',
            ]
        )
        ->arg(
            '$persistenceManager',
            service(PersistenceManager::class)
        )
    ;

    $services
        ->set(DeliveryNumber::class)
        ->tag(
            'event.listener',
            [
                'event' => NumberGeneratorEvent::class,
                'identifier' => 'cart--order--create--delivery-number',
                'after' => 'cart--order--create--order',
            ]
        )
        ->arg(
            '$persistenceManager',
            service(PersistenceManager::class)
        )
    ;

    $services
        ->set(ClearCart::class)
        ->tag(
            'event.listener',
            [
                'event' => FinishEvent::class,
                'identifier' => 'cart--order--finish--clear-cart',
                'after' => 'cart--order--finish--email',
            ]
        )
    ;

    $services
        ->set(Email::class)
        ->tag(
            'event.listener',
            [
                'event' => FinishEvent::class,
                'identifier' => 'cart--order--finish--email',
            ]
        )
    ;

    $services
        ->set(LogServiceUpdate::class)
        ->tag(
            'event.listener',
            [
                'event' => UpdateServiceEvent::class,
                'identifier' => 'cart--order--update--log-service-update',
            ]
        )
    ;
};
