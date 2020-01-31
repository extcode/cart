.. include:: ../../Includes.txt

=====================================
Feature: #241 - Add Service Interface
=====================================

See :issue:`241`

Description
===========

The service is moved to an own class implementing the `\Extcode\Cart\Domain\Model\Cart\ServiceInterface`.
If the flex price configuration via TypoScript doesn't fit for the shop, an own service class can calculate
the correct price.

More information about the flex price configuration and the service interface can be found
in the :ref:`configuration_shipping_method_flex_price` documentation.

.. index:: Fluid, Frontend, API
