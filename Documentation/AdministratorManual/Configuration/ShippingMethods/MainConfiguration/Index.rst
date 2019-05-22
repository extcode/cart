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

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.preset
   Data type
      int
   Description
      Defines which shipping method is selected by default if the user has not yet selected another shipping method.
      If the shipping method is not defined when the destination country is changed, the shipping method defined here for the destination country is also selected.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n
   Data type
      array
   Description
      You can configure N different shipping methods.
   Default
      options.1

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.title
   Data type
      string
   Description
      Name of the shipping type (for example: Standard, Express).

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.extra
   Data type
      float
   Description
      Shipping costs that are to be billed to the customer (for example: 1.50).
      The currency depends on the standard configuration.
   Default
      0.00

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.taxClass
   Data type
      int
   Description
      ID of the tax class for this shipping method.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.status
   Data type
      string
   Description
      The status that the order with this shipping method should have by default.

