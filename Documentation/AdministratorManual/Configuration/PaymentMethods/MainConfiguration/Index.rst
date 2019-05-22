.. include:: ../../../../Includes.txt

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

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.preset
   Data type
      int
   Description
      Defines which payment method is selected by default if the user has not yet selected a different payment method.
      If the payment method is not defined when changing the country of account, the payment method defined here for the country of invoice will also be selected.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.N
   Data type
      array
   Description
      You can configure N different payment methods.
   Default
      options.1

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.N.title
   Data type
      string
   Description
      Name of the payment method (e.g.: prepayment, cash on delivery).

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.extra
   Data type
      float
   Description
      Costs for the payment method to be billed to the customer (for example, 1.50).
      The currency depends on the standard configuration.
   Default
      0.00

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.taxClass
   Data type
      int
   Description
      ID of the tax class for this payment method.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.status
   Data type
      string
   Description
      The status that the order with this payment method should have by default.
