.. include:: ../../../../Includes.txt
.. _adminstration_shippingmethods_mainconfiguration:

==================
Main configuration
==================

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

       shippings {
           countries {
              de {
                  preset = 1
                  options {
                      1 {
                          title = Standard
                          extra = 0.00
                          taxClassId = 1
                          status = open
                      }
                  }
              }
              at < .de
              ch < .de
           }
       }
   }

plugin.tx_cart.shippings.countries
==================================

.. confval:: de.preset

   :Type: int

   Defines which shipping method is selected by default if the user has not yet selected another shipping method.
   If the shipping method is not defined when the destination country is changed, the shipping method defined here for the destination country is also selected.

.. confval:: de.options.n

   :Type: array
   :Default: options.1

   You can configure N different shipping methods.

.. confval:: de.options.n.title

   :Type: string

   Name of the shipping type (for example: Standard, Express).

.. confval:: de.options.n.extra

   :Type: float
   :Default: 0.00

   Shipping costs that are to be billed to the customer (for example: 1.50).
   The currency depends on the standard configuration.

.. confval:: de.options.n.taxClassId

   :Type: int

   ID of the tax class for this payment method. The taxClassId must either be assignable to a defined tax class.
   However, the values `-1` and `-2` are also allowed here.
   `-1` The tax class for the calculation is based on the largest tax class of the products in the shopping cart.
   `-2` The taxes are calculated as a percentage of the tax of the products in the shopping cart.

.. confval:: de.options.n.status

   :Type: string

   The status that the order with this shipping method should have by default.

