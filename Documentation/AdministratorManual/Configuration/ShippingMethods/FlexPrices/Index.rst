.. include:: ../../../../Includes.txt

Flex prices
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

                          extraType = by_number_of_physical_products
                          extras {
                              1 {
                                  value = 0
                                  extra = 1.50
                              }
                              2 {
                                  value = 1
                                  extra = 3.00
                              }
                              3 {
                                  value = 4
                                  extra = 4.00
                              }
                              4 {
                                  value = 8
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

|

.. container:: table-row

   Property
      plugin.tx_cart.shipping.countries.de.options.N.extraType
   Data type
      string
   Description
      * by_price:
      * by_quantity:
      * by_number_of_physical_products:
      * by_number_of_virtual_products:
      * by_number_of_all_products:
      * by_service_attribute_1_sum:
      * by_service_attribute_1_max:
      * by_service_attribute_2_sum:
      * by_service_attribute_2_max:
      * by_service_attribute_3_sum:
      * by_service_attribute_3_max:

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

::

   plugin.tx_cart {
       shippings {
           countries {
              de {
                  preset = 1
                  options {
                      1 {
                          title = Standard
                          className = \\MyVendor\MyExtension\Service
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

|

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