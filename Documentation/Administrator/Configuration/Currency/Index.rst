.. include:: ../../../Includes.txt

.. _currency:

========
Currency
========

Currency Format
===============

To make the templates a bit easier to use, TypoScript can be used to configure
the format specifications for the price output.
These TypoScript specifications are then used in the `<cart:format.currency>`
ViewHelper.

.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/constants.typoscript

   plugin.tx_cart {
       settings {
           format {
               currency {
                   currencySign       = &euro;
                   decimalSeparator   = ,
                   thousandsSeparator = .
                   prependCurrency    = false
                   separateCurrency   = true
                   decimals           = 2
               }
           }
       }
   }

.. _adminstration_currency_translation:

Currency Translation
====================

If you want to allow the user to display the prices in a different currency
in the store, you can configure this via TypoScript.

.. NOTE::
   At the moment there is no automatism to update the factor and adjust it to a
   current value. With a scheduler task, it should be possible to connect a
   corresponding service quite quickly.

.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/setup.typoscript

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

plugin.tx_cart.settings.currency
--------------------------------

.. confval:: preset

   :Type: int
   :Default: 1

   Defines which of the existing currencies will be the default currency
   of a new shopping cart.

.. confval:: options.<n>

   :Type: array

   List of the different currencies available.

.. confval:: options.<n>.code

   :Type: array
   :Default: EUR

   Three-digit international currency code according
   to `ISO 4217 (Wikipedia) <https://de.wikipedia.org/wiki/ISO_4217>`_.
   This is among other things for different payment providers and as a
   parameter for changing the currency in the shopping cart.


.. confval:: options.<n>.sign

   :Type: array
   :Default: €

   Currency symbol, if available, for the currency.

.. confval:: options.<n>.translation

   :Type: array
   :Default: 1.0

   Currency conversion factor.
   The price of the products is divided by this factor.
