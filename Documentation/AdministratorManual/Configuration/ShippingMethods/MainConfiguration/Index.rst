.. include:: ../../../../Includes.txt

Main configuration
==================

::

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
      plugin.tx_cart.shippings.countries.de.options.n.taxClassId
   Data type
      int
   Description
      ID of the tax class for this payment method. The taxClassId must either be assignable to a defined tax class.
      However, the values `-1` and `-2` are also allowed here.
      `-1` The tax class for the calculation is based on the largest tax class of the products in the shopping cart.
      `-2` The taxes are calculated as a percentage of the tax of the products in the shopping cart.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.status
   Data type
      string
   Description
      The status that the order with this shipping method should have by default.

