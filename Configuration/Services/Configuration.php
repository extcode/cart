<?php

declare(strict_types=1);

use Extcode\Cart\Configuration\Loader\CurrencyTranslationLoader;
use Extcode\Cart\Configuration\Loader\CurrencyTranslationLoaderInterface;
use Extcode\Cart\Configuration\Loader\PaymentMethodsLoaderInterface;
use Extcode\Cart\Configuration\Loader\ShippingMethodsLoaderInterface;
use Extcode\Cart\Configuration\Loader\SiteSets\PaymentMethodsLoader as SiteSetsPaymentMethodsLoader;
use Extcode\Cart\Configuration\Loader\SiteSets\ShippingMethodsLoader as SiteSetsShippingMethodsLoader;
use Extcode\Cart\Configuration\Loader\SiteSets\TaxClassLoader as SiteSetsTaxClassLoader;
use Extcode\Cart\Configuration\Loader\TaxClassLoaderInterface;
use Extcode\Cart\Configuration\Loader\TypoScript\PaymentMethodsLoader as TypoScriptPaymentMethodsLoader;
use Extcode\Cart\Configuration\Loader\TypoScript\ShippingMethodsLoader as TypoScriptShippingMethodsLoader;
use Extcode\Cart\Configuration\Loader\TypoScript\SpecialOptionsLoader;
use Extcode\Cart\Configuration\Loader\TypoScript\TaxClassLoader as TypoScriptTaxClassLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    $services = $containerConfigurator
        ->services()
    ;

    $services
        ->alias(
            CurrencyTranslationLoaderInterface::class,
            CurrencyTranslationLoader::class
        )
    ;

    $services
        ->alias(
            PaymentMethodsLoaderInterface::class,
            TypoScriptPaymentMethodsLoader::class
        )
    ;

    $services
        ->set(SiteSetsPaymentMethodsLoader::class)
        ->public()
    ;

    $services
        ->set(TypoScriptPaymentMethodsLoader::class)
        ->public()
    ;

    $services
        ->alias(
            ShippingMethodsLoaderInterface::class,
            TypoScriptShippingMethodsLoader::class
        )
    ;

    $services
        ->set(SiteSetsShippingMethodsLoader::class)
        ->public()
    ;

    $services
        ->set(TypoScriptShippingMethodsLoader::class)
        ->public()
    ;

    $services
        ->set(SpecialOptionsLoader::class)
        ->public()
    ;

    $services
        ->alias(
            TaxClassLoaderInterface::class,
            TypoScriptTaxClassLoader::class
        )
    ;

    $services
        ->set(SiteSetsTaxClassLoader::class)
        ->public()
    ;

    $services
        ->set(TypoScriptTaxClassLoader::class)
        ->public()
    ;
};
