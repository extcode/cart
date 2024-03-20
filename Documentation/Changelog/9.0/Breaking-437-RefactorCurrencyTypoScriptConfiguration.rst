.. include:: ../../Includes.txt

===========================================================
Breaking: #437 - Refactor Currency TypoScript Configuration
===========================================================

See :issue:`413`

Description
===========

Rendering the currency selector renders the `default = 1` from TypoScript as
an own option. Using a `preset` and an list of `options` like in shipping and
payment configuration will fix this bug.

Affected Installations
======================

All installations using an own configuration for `plugin.tx_cart.setting.currencies` or
override the `Resources/Private/Partials/Cart/CurrencyForm.html` partial.

Migration
=========

TypoScript
----------

Adapt the new TypoScript configuration structure. Instead of:

.. code-block:: typoscript

   plugin.tx_cart {
       settings {
           currencies {
               default = 1
               1 {
                   code = EUR
                   sign = €
                   translation = 1.00
               }
           }
       }
   }

use a structure like this:

.. code-block:: typoscript

   plugin.tx_cart {
       settings {
           currencies {
               preset = 1
               options {
                   1 {
                       code = EUR
                       sign = €
                       translation = 1.00
                   }
               }
           }
       }
   }

Templates (Partials)
--------------------

Use `settings.currencies.options` instead of `settings.currencies` in the partial.


.. index:: TypoScript, Template, Frontend
