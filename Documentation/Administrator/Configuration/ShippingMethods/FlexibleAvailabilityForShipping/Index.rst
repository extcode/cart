.. include:: ../../../../Includes.txt

..  _shipping_method_flex_availablility:

============================
Flexible availability for shipping
============================

It can be a requirement that the shipping method availability should depend on
the products in the shopping cart. For these cases the extension provides a default
implementation (see `cart/Classes/Domain/Model/Cart/Service.php`) with
configuration options very similar to
 :ref:`Flexible prices for shipping <shipping_method_flex_price>`.
The implementation can be adapted to own needs by using the `ServiceInterface`
also described in :ref:`Flexible prices for shipping <shipping_method_flex_price>`.

.. code-block:: typoscript
   :caption: Can be set in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
    shippings {
        countries {
            de {
                preset = 1
                options {
                    1 {
                        title = PDF/Email
                        taxClassId = 1
                        fallBackId = 2
                        status = open
                        free {
                            from = 0
                        }
                        available = by_number_of_physical_products
                        available {
                            0 {
                                value = 0
                                available = 1
                            }
                            2 {
                                value = 1
                                available = 0
                            }
                        }
                    }

                    2 {
                        title = Standard Delivery
                        taxClassId = 1
                        fallBackId = 1
                        status = open
                        extra = 99.00
                        available = by_number_of_physical_products
                        available {
                            0 {
                                value = 0
                                available = 0
                            }
                            2 {
                                value = 1
                                available = 1
                            }
                        }
                    }
                }
            }
        }
   }

plugin.tx_cart.shipping.countries.de
====================================
.. confval:: options.<n>.available

   :Type: string

   ============================== ===========================================
   Setting                        Explanation what is used for the comparison
   ============================== ===========================================
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


.. confval:: options.<n>.available.<m>.value

   :Type: int

   Defines the matching condition.

.. confval:: options.<n>.available.<m>.available

   :Type: float

   Boolean value to define whether the shipping method is available.
