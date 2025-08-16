.. include:: ../../../Includes.rst.txt

.. _shipping_methods:

================
Shipping methods
================

The shipping methods are defined via TypoScript for each shopping cart.
The standard template already comes with a shipping method (standard)
as shown below. In case the shipping method(s) for several countries shall
be configured, please also check the possibility to use a 'zone' based 
configuration. This reduces the amount of configuration necessary. 

With the definition `plugin.tx_cart.settings.allowedCountries` the output of
the selector in the shopping cart is defined. The options can also be
translated by an own `SelectViewhelper`.

.. important::
   If different shipping methods are defined for different countries, the
   country of the delivery address is used. Only in the case that no different
   delivery address has been specified, the selection of the billing address
   is used as a basis.


Configuration given by this extension
=====================================

.. important::
   The provided TypoScript of the extension provides the following
   configuration for the German-speaking area.

.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       settings {
           countries {
               preset = 1
               options {
                   1 {
                       code = de
                       label = Deutschland
                   }
                   2 {
                       code = at
                       label = Österreich
                   }
                   3 {
                       code = ch
                       label = Schweiz
                   }
               }
           }
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
----------------------------------

.. confval:: shippings.countries.<country-code>.preset

   :Type: int

   Defines which shipping method is selected by default if the user has not yet
   selected another shipping method.

   If the shipping method is not defined when the destination country is
   changed, the shipping method defined here for the destination country is
   also selected.

.. confval:: shippings.countries.<country-code>.options.<n>

   :Type: array
   :Default: options.1

   You can configure n different shipping methods.

.. confval:: shippings.countries.<country-code>.options.<n>.title

   :Type: string

   Name of the shipping type (for example: Standard, Express).

.. confval:: shippings.countries.<country-code>.options.<n>.extra

   :Type: float
   :Default: 0.00

   Shipping costs that are to be billed to the customer (for example: 1.50).
   The currency depends on the standard configuration.

.. confval:: shippings.countries.<country-code>.options.<n>.taxClassId

   :Type: int

   ID of the tax class for this payment method. The taxClassId must either be
   assignable to a defined tax class.

   However, the values `-1` and `-2` are also allowed here. These options can be
   used when the shop has products with multiple tax classes.

   For example in Germany and Austria the shipping costs are an ancillary
   service of the order. Therefore the tax of the shipping costs needs to be
   calculated.

   * `-1` → The products within the shopping cart are taken. Of those products
     the tax class with the highest value is then taken as tax class for the
     calculation of the shipping costs.
   * `-2` → The products within the shopping cart are taken. The tax for the
     shipping costs is calculated as a percentage of the tax of those products.

.. confval:: shippings.countries.<country-code>.options.<n>.status

   :Type: string

   The status that the order with this shipping method should have by default.

.. _country-configuration:

Country Configuration
=====================

.. code-block:: typoscript
   :caption: can be set in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       shippings {
           countries {
              de {
                  options {
                      2 {
                          free {
                              from = 50
                              until = 100
                          }
                          available {
                              from = 20
                              until = 200
                          }
                          fallBackId = 1
                      }
                  }
              }
           }
       }
   }

plugin.tx_cart.shippings.countries
----------------------------------

.. confval:: shippings.countries.<country-code>.options.<n>.free.from

   :Type: float

   If the gross price of the products is greater than or equal to the
   specified value, the price for the shipping method is 0.00.
   This can be used for free shipping from a defined order value.

.. confval:: shippings.countries.<country-code>.options.<n>.free.until

   :Type: float

   If the gross price of the products is less than or equal to the specified
   value, the price for the shipping method is 0.00.

.. confval:: shippings.countries.<country-code>.options.<n>.available.from

   :Type: float

   Only if the gross price of the products is greater than or equal to the
   specified value, this shipping method is available, otherwise the
   fallback shipping method will be used.

.. confval:: shippings.countries.<country-code>.options.<n>.available.until

   :Type: float

   Only if the gross price of the products is less than or equal to the
   specified value, this shipping method is available, otherwise the
   fallback shipping method will be used.

.. confval:: shippings.countries.<country-code>.options.<n>.fallBackId

   :Type: int

   If the shipping method is not available, the shipping method with this
   ID will be used.

Zone configuration
==================

plugin.tx_cart.shippings.zones
------------------------------

The configuration of shipping zones can be used to ease the configuration
of shippings for multiple countries. Their usage saves a lot of configuration
work, if you want to deliver to many countries.
In case both country configuration as well as zone configuration are valid for
a dedicated country, the country configuration has precedence over the zone configuration. 

.. NOTE::
   * The system first searches for a suitable configuration in the list of country configurations.
   * The list of zone configurations is then looked through. The first matching configuration is used.
   * The country-based shipping configuration coming with the extension can interfere with your zone configuration for shippings. 
     Therefore, check if you need to remove the country-based configuration when using zones as shown below.

.. code-block:: typoscript
   :caption: Can be set in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       shippings {
           // this removes any previous country based shipping configuration
           countries >
           zones {
               1 {
                  preset = 1
                  countries = de
                  options {
                        1 {
                           title = Shipment Germany
                           extra = by_price
                           extra {
                               1  {
                                   value = 0.00
                                   extra = 3.70
                                  }
                               2  {
                                   value = 100.00
                                   extra = 0.00
                                  }
                                 }
                               taxClassId = 1
                               status = open
                          }
                        2 {
                           title = Collect at shop
                           extra = 0.00
                           taxClassId = 1
                           status = open
                          }
                     }
                 }
               2 {
                  preset = 1
                  countries = at,ch
                  options {
                        1 {
                           title = International Shipment
                           extra = by_price
                           extra {
                               1  {
                                   value = 0.00
                                   extra = 5.90
                                  }
                               2  {
                                   value = 100.00
                                   extra = 0.00
                                  }
                                }
                           taxClassId = 1
                           status = open
                          }
                        2 {
                           title = Collect at shop
                           extra = 0.00
                           taxClassId = 1
                           status = open
                          }
                      }
                  }
               }                          
           }
       }
|

.. confval:: shippings.zones.<n>

   :Type: int

   You can configure up to n different zones.

.. confval:: shippings.zones.<n>.countries

   :Type: array

   List of countries for which this configuration is valid.

Deactivate shipping methods
===========================

At the moment, the existence of a shipping method is required for the processing
of an order. That means that at least one shipping method must be specified.
In this case the selection block for the payment can be hidden.

.. code-block:: typoscript

   plugin.tx_cart {
        settings {
            showCartAction {
                showPartials {
                   shippingMethodForm = false
                }
            }
        }
   }

See :ref:`plugin.tx_cart.settings.showCartAction.showPartials.shippingMethodForm <plugin_tx_cart_settings_showCartAction_showPartials_shippingMethodForm>`

.. toctree::
   :maxdepth: 5
   :titlesonly:

   FlexiblePricesForShipping/Index
