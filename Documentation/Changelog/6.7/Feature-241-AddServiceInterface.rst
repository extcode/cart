.. include:: ../../Includes.rst.txt

=====================================
Feature: #241 - Add Service Interface
=====================================

See `Issue 241 <https://github.com/extcode/cart/issues/241>`__

Description
===========

The service is moved to an own class implementing the `\Extcode\Cart\Domain\Model\Cart\ServiceInterface`.
If the flex price configuration via TypoScript doesn't fit for the shop, an own service class can calculate
the correct price.

.. index:: Fluid, Frontend, API
