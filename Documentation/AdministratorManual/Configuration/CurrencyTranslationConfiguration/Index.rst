.. include:: ../../../Includes.txt
.. _adminstration_configuration_currencytranslationsconfiguration:

==================================
Currency Translation Configuration
==================================

If you want to allow the user to display the prices in a different currency in the store, you can configure this via TypoScript.

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

plugin.tx_cart.settings.currency
================================

.. confval:: default

   :Type: int
   :Default: 1

   Defines which of the existing currencies will be the default currency
   of a new shopping cart.

.. confval:: n

   :Type: array

   List of the different currencies available.

.. confval:: n.code

   :Type: array
   :Default: EUR

   Three-digit international currency code according
   to `ISO 4217 (Wikipedia) <https://de.wikipedia.org/wiki/ISO_4217>`_.
   This is among other things for different payment providers and as a
   parameter for changing the currency in the shopping cart.


.. confval:: n.sign

   :Type: array
   :Default: €

   Currency symbol, if available for the currency.

.. confval:: n.translation

   :Type: array
   :Default: 1.0

   Currency conversion factor. The price of the products is divided by this factor.

.. NOTE::
   At the moment there is no automatism to update the factor and adjust it to a
   current value. With scheduler task, it should be possible to connect a
   corresponding service quite quickly.
