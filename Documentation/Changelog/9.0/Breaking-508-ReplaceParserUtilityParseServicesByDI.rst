.. include:: ../../Includes.txt

===========================================================
Breaking: #508 - Replace ParserUtility::parseServices by DI
===========================================================

See :issue:`480`

Description
===========

The existing `\Extcode\Cart\Utility\ParserUtility::parseServices()` used a
a TypoScript Configuration to allow to use an own implementation to parse and
provide available payment and shipping methods or special options.
The change removes the `ParserUtility` class and injecting the following
services:
- `\Extcode\Cart\Service\PaymentMethodsServiceInterface`,
- `\Extcode\Cart\Service\ShippingMethodsServiceInterface`,
- `\Extcode\Cart\Service\SpecialOptionsServiceInterface`
instead.

Affected Installations
======================

All installations where
- `plugin.tx_cart.payments.className`,
- `plugin.tx_cart.shippings.className`,
- `plugin.tx_cart.specials.className`
are used to replace the default services.

Migration
=========

Remove the old configuration from TypoScript.
Add an entry to your `Services.yaml` or `Services.php` and configure your
implementation of the service interface for the constructor arguments.

.. index:: Backend, Dependency Injection
