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

.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.default
   Data type
      int
   Description
      Defines which of the existing currencies will be the default currency
      of a new shopping cart.
   Default
      1

.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.n
   Data type
      array
   Description
      List of the different currencies available.

.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.n.code
   Data type
      array
   Description
      Three-digit international currency code according
      to `ISO 4217 (Wikipedia) <https://de.wikipedia.org/wiki/ISO_4217>`_.
      This is among other things for different payment providers and as a
      parameter for changing the currency in the shopping cart.
   Default
      EUR

.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.n.sign
   Data type
      array
   Description
      Currency symbol, if available for the currency.
   Default
      €

.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.n.translation
   Data type
      array
   Description
      Currency conversion factor. The price of the products is divided by this factor.
   Default
      1.0

.. NOTE::
   At the moment there is no automatism to update the factor and adjust it to a
   current value. With scheduler task, it should be possible to connect a
   corresponding service quite quickly.
