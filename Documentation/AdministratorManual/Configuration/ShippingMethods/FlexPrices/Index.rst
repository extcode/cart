.. include:: ../../../../Includes.txt

..  _configuration_shipping_method_flex_price:

Flex Prices
===========

Sometimes you won't configure fix prices for an service methods. Especially the shipping costs can
depend on the amount of products in cart. The cart extension provides a service interface and a
default implementation which can handle different kinds of dependencies.
A common condition is the amount of physical products in cart.

::

   plugin.tx_cart {
       shippings {
           countries {
              de {
                  preset = 1
                  options {
                      1 {
                          title = Standard

                          extra = by_price
                          extra {
                              1 {
                                  value = 0.00
                                  extra = 1.50
                              }
                              2 {
                                  value = 5.00
                                  extra = 3.00
                              }
                              3 {
                                  value = 10.00
                                  extra = 4.00
                              }
                              4 {
                                  value = 80.00
                                  extra = 5.00
                              }
                          }
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
      plugin.tx_cart.shipping.countries.de.options.N.extra
   Data type
      string
   Description
      * by_price
      * by_price_of_physical_products
      * by_quantity
      * by_number_of_physical_products
      * by_number_of_virtual_products
      * by_number_of_all_products
      * by_service_attribute_1_sum
      * by_service_attribute_1_max
      * by_service_attribute_2_sum
      * by_service_attribute_2_max
      * by_service_attribute_3_sum
      * by_service_attribute_3_max

.. container:: table-row

   Property
      plugin.tx_cart.shipping.countries.de.options.N.extra.M.value
   Data type
      int
   Description
      Defines the matching condition.

.. container:: table-row

   Property
      plugin.tx_cart.shipping.countries.de.options.N.extra.M.extra
   Data type
      float
   Description
      Defines the extra value.

Extending Service Calculation
-----------------------------

Sometimes the shipping method has some special rules.
In Germany the Post provides the so called "Bücherversand" for books. Some rules
apply to this shipping method.

- The weight is included in the price calculation.
- Certain lengths and widths must not be exceeded.
- Furthermore only books may be sent.

Such special rules cannot be mapped using TypoScript configurations. Therefore, a
separate service class can be implemented for a method, which can then return the
calculated price to the shopping cart.

::

   plugin.tx_cart {
       shippings {
           countries {
              de {
                  preset = 1
                  options {
                      1 {
                          title = Standard
                          className = \\MyVendor\\MyExtension\\MyShippingService
                          taxClassId = 1
                          status = open
                      }
                  }
              }
           }
       }
   }

.. container:: table-row

   Property
      plugin.tx_cart.shipping.countries.de.options.N.className
   Data type
      string
   Description
      Defines the class which implements the `\Extcode\Cart\Domain\Model\Cart\ServiceInterface`.
      This allows you to implement your own service cost calculation per service method.
      A valid case is, that you have to decide between `serviceAttribute1` (e.g. weight) and
      `service_attribute2` (e.g. size). This is not configurable through TypoScript and heighly
      depends on your delivery service.
