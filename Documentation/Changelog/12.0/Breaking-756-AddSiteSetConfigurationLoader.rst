.. include:: ../../Includes.rst.txt

==================================================
Breaking: #756 - Add Site Set Configuration Loader
==================================================

See `Issue 756 <https://github.com/extcode/cart/issues/756>`__

Description
===========

Regarding to the new feature configuring cart by site sets the
code was restructured and the services to load configurations
moved to `Configuration/Loader`.

The interfaces was moved form `Service` to `Configuration`:

`Classes/Service/TaxClassServiceInterface.php` => `Classes/Configuration/Loader/TaxClassLoaderInterface.php`
`Classes/Service/PaymentMethodsServiceInterface.php` => `Classes/Configuration/Loader/PaymentMethodsLoaderInterface.php`
`Classes/Service/ShippingMethodsServiceInterface.php` => `Classes/Configuration/Loader/ShippingMethodsLoaderInterface.php`
`Classes/Service/SpecialOptionsServiceInterface.php` => `Classes/Configuration/Loader/SpecialOptionsLoaderInterface.php`

The classes was moved form `Service` to `Configuration` or `Configuration/TypoScript`:

`Classes/Service/PaymentMethodsFromTypoScriptService.php` => `Classes/Configuration/Loader/TypoScript/PaymentMethodsLoader.php`
`Classes/Service/ShippingMethodsFromTypoScriptService.php` => `Classes/Configuration/Loader/TypoScript/ShippingMethodsLoader.php`
`Classes/Service/SpecialOptionsFromTypoScriptService.php` => `Classes/Configuration/Loader/TypoScript/SpecialOptionsLoader.php`
`Classes/Service/TaxClassService.php` => `Classes/Configuration/Loader/TypoScript/TaxClassLoader.php`

Affected Installations
======================

All installations implementing one of the service interfaces or extending one of the
services.

Migration
=========

Use interfaces from new namespace.

.. CAUTION::
   All configuration loaders are now final. If your own configuration loader only extends the
   service, you have to copy the code from the final configuration loader class and add your
   changes in your own class. Add or update an alias to your own configuration loader class.
