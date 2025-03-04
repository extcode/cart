.. include:: ../../Includes.rst.txt

======================================================
Feature: #638 - Move currency translation to a service
======================================================

See `Issue 638 <https://github.com/extcode/cart/issues/638>`__

Description
===========

Currency conversion is currently very strongly linked to the cart model and the
way in which the currency conversion factor is loaded from TypoScript.

In order to be able to obtain the conversion factor from other sources, the
calculation is carried out by the `CurrencyTranslationService`. This service is
instantiated via the `CurrencyTranslationServiceInterface` interface so that a
corresponding exchange via DI is possible.

The implementation should only be a start and does not yet offer a stable API
because the behaviour must remain the same within the published versions. For
this reason, the `CurrencyTranslationServiceInterface` is marked as `@internal`.
Use is at your own risk! Changes to the service interface must be observed
independently in the event of updates!

Impact
======

No direct impact.

.. index:: API
