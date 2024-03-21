.. include:: ../../Includes.txt

===================================================================
Breaking: #437 - Refactor allowedCountries TypoScript Configuration
===================================================================

See :issue:`413`

Description
===========

In order to streamline the TypoScript configuration the configuration
for allowedCountries was changed.

Affected Installations
======================

All installations using an own configuration for `plugin.tx_cart.setting.allowedCountries` or
override the `Resources/Private/Partials/Cart/OrderForm/Address/Billing.html` or
`Resources/Private/Partials/Cart/OrderForm/Address/Shipping.html` partial.


Migration
=========

TypoScript
----------

Adapt the new TypoScript configuration structure. Instead of:

.. code-block:: typoscript

   plugin.tx_cart {
       settings {
           allowedCountries {
               de = Deutschland
               at = Österreich
               ch = Schweiz
           }
           defaultCountry = de
       }
   }

use a structure like this:

.. code-block:: typoscript

   plugin.tx_cart {
       settings {
           countries {
               preset = 1
               options {
                   1 {
                       code = de
                       label = Deutschland
                   }
                   2 {
                       code = at
                       label = Österreich
                   }
                   3 {
                       code = ch
                       label = Schweiz
                   }
               }
           }
       }
   }

Templates (Partials)
--------------------

Use `settings.countries.options` instead of `settings.allowedCountries` in the partial,
and add both `optionLabelField="label"` and `optionValueField="code"` to the country
selectors.

.. index:: TypoScript, Template, Frontend
