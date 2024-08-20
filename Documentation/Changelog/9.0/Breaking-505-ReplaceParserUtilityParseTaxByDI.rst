.. include:: ../../Includes.rst.txt

======================================================
Breaking: #505 - Replace ParserUtility::parseTax by DI
======================================================

See :issue:`480`

Description
===========

The existing `\Extcode\Cart\Utility\ParserUtility::parseTax()` uses a TaxClassService
which could be configured by a TypoScript Configuration. The change remove the
method from the class and injecting the `Extcode\Cart\Service\TaxClassServiceInterface`
by Dependency Injection.

Affected Installations
======================

All installations where `plugin.tx_cart.taxClasses.className` is used to replace
the default TaxClassService.

Migration
=========

Remove the old configuration from TypoScript.
Add an entry to your `Services.yaml` or `Services.php` and configure your
implementation of the `Extcode\Cart\Service\TaxClassServiceInterface` for the
`$taxClassService` constructor argument.

.. index:: Backend, Dependency Injection
