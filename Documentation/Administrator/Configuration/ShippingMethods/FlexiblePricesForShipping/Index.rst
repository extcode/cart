.. include:: ../../../../Includes.txt

..  _shipping_method_flex_price:

============================
Flexible prices for shipping
============================

It can be a requirement that the shipping costs should depend on the products in
the shopping cart. For these cases the extension provides a default
implementation (see `cart/Classes/Domain/Model/Cart/Service.php`). The
implementation can be adapted to own needs by using the `ServiceInterface` (
which is described in the section below).

.. code-block:: typoscript
   :caption: Can be set in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript

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
                              // From 0,00 € to 4,99 € the shipping costs will be 1,50 €
                              1 {
                                  value = 0.00
                                  extra = 1.50
                              }
                              // From 5,00 € to 9,99 € the shipping costs will be 3,00 €
                              2 {
                                  value = 5.00
                                  extra = 3.00
                              }
                              // From 10,00 € to 79,99 € the shipping costs will be 4,00 €
                              3 {
                                  value = 10.00
                                  extra = 4.00
                              }
                              // Above 80,00 € the shipping costs will be 5,00 €
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

plugin.tx_cart.shipping.countries.de
====================================
.. confval:: options.<n>.extra

   :Type: string

   ============================== ===========================================
   Setting                        Explanation what is used for the comparison
   ============================== ===========================================
   each                           This option is somehow special as simply multiplies the amount of products in the cart with a static value (see example below).
   by_price                       The total price of the shopping cart.
   by_price_of_physical_products  The total price of physical products in the shopping cart (EXT:cart_products allows to define products as "virtual producs").
   by_quantity                    The amount of physical(!) products. (This is a synonym for `by_number_of_physical_products`)
   by_number_of_physical_products The amount of physical products
   by_number_of_virtual_products  The amount of virtual products
   by_number_of_all_products      The amount of all products
   by_service_attribute_1_sum     The sum of the values of `Service Attribute 1` of all products in the cart.
   by_service_attribute_1_max     The highest value of `Service Attribute 1` of all product in the cart.
   by_service_attribute_2_sum     The sum of the values of `Service Attribute 2` of all products in the cart.
   by_service_attribute_2_max     The highest value of `Service Attribute 2` of all product in the cart.
   by_service_attribute_3_sum     The sum of the values of `Service Attribute 3` of all products in the cart.
   by_service_attribute_3_max     The highest value of `Service Attribute 3` of all product in the cart.
   ============================== ===========================================

   You can also define free shipping, see :ref:`plugin.tx_cart.shippings.countries <country-configuration>`.

   .. code-block:: typoscript
      :caption: Example for `extra` set to `each`

      // shippingCosts = 'amount of products in the cart' x 1.50
      plugin.tx_cart.shippings.countries.de.options.1 {
          title = Depending on amount of articles in cart
          extra = each
          extra {
              extra = 1.50
          }
          taxClassId = 1
          status = open
      }

.. confval:: options.<n>.extra.<m>.value

   :Type: int

   Defines the matching condition.

.. confval:: options.<n>.extra.<m>.extra

   :Type: float

   Defines the value that will be used for the shipping costs.


Extending Service Calculation
-----------------------------

Sometimes the shipping method has some special rules.
In Germany the post provides the so called "Bücherversand" for books. Some rules
apply to this shipping method.

- The weight is included in the price calculation.
- Certain lengths and widths must not be exceeded.
- Furthermore only books may be sent.

Such special rules cannot be mapped using TypoScript configurations. Therefore,
a separate service class can be implemented for a method, which can then return
the calculated price to the shopping cart.

.. code-block:: typoscript
   :caption: Can be set in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript

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

.. confval:: options.<n>.className

   :Type: string

   Defines the class which implements the `\Extcode\Cart\Domain\Model\Cart\ServiceInterface`.
   This allows you to implement your own service cost calculation per service
   method.

   A valid case is, that you have to decide between `serviceAttribute1`
   (e.g. weight) and `service_attribute2` (e.g. size). This is not configurable
   with TypoScript and highly depends on your delivery service. Therefore a
