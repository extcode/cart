.. include:: ../../../../Includes.txt
.. _adminstration_paymentmethods_mainconfiguration:

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

       payments {
           countries {
               de {
                   preset = 1
                   options {
                       1 {
                           title = Vorkasse
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

plugin.tx_cart.payments.countries
=================================

.. confval:: de.preset

   :Type: int

   Defines which payment method is selected by default if the user has not yet selected a different payment method.
   If the payment method is not defined when changing the country of account, the payment method defined here for the country of invoice will also be selected.

.. confval:: de.options.N

   :Type: array
   :Default: options.1

   You can configure N different payment methods.

.. confval:: de.options.N.title

   :Type: string

   Name of the payment method (e.g.: prepayment, cash on delivery).

.. confval:: de.options.n.extra

   :Type: float
   :Default: 0.00

   Costs for the payment method to be billed to the customer (for example, 1.50).
   The currency depends on the standard configuration.

.. confval:: de.options.n.taxClassId

   :Type: int

   ID of the tax class for this payment method. The taxClassId must either be assignable to a defined tax class.
   However, the values `-1` and `-2` are also allowed here.
   `-1` The tax class for the calculation is based on the largest tax class of the products in the shopping cart.
   `-2` The taxes are calculated as a percentage of the tax of the products in the shopping cart.

.. confval:: de.options.n.status

   :Type: string

   The status that the order with this payment method should have by default.
