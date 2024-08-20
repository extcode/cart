.. include:: ../../Includes.rst.txt

======================================
Feature: #277 - Add TaxClass Interface
======================================

See :issue:`277`

Description
===========

Parsing the tax classes from TypoScript is moved to an own class implementing of the `\Extcode\Cart\Service\TaxClassServiceInterface`.
If the tax classes for a cart comes from an API the Interface can be used to load the tax classes through .


.. index:: Backend, API
