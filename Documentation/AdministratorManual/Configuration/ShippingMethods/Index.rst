.. include:: ../../../Includes.txt

================
Shipping methods
================

The shipping methods are defined via TypoScript for each shopping cart.
The standard template already comes with a shipping method (standard)
as shown below.

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

plugin.tx_cart.settings
-----------------------
The parameter `plugin.tx_cart.settings.defaultCountry`

.. confval:: defaultCountry

   :Type: string
   :Default: de

   Defines which country will be preselected.

plugin.tx_cart.shippings.countries
----------------------------------

.. confval:: de.preset

   :Type: int

   Defines which shipping method is selected by default if the user has not yet
   selected another shipping method.

   If the shipping method is not defined when the destination country is
   changed, the shipping method defined here for the destination country is
   also selected.

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

   ID of the tax class for this payment method. The taxClassId must either be
   assignable to a defined tax class.

   However, the values `-1` and `-2` are also allowed here.

   * `-1` → The tax class for the calculation is based on the largest tax class
     of the products in the shopping cart.
   * `-2` → The taxes are calculated as a percentage of the tax of the products
     in the shopping cart.

.. confval:: de.options.n.status

   :Type: string

   The status that the order with this shipping method should have by default.


Country Configuration
=====================

plugin.tx_cart.shippings.countries
----------------------------------

.. confval:: de.options.n.free.from

   :Type: float

   If the gross price of the products is greater than or equal to the
   specified value, the price for the shipping method is 0.00.
   This can be used for free shipping from a defined order value.

.. confval:: de.options.n.free.until

   :Type: float

   If the gross price of the products is less than or equal to the specified
   value, the price for the shipping method is 0.00.

.. confval:: de.options.n.available.from

   :Type: Text

   Only if the gross price of the products is greater than or equal to the
   specified value, this shipping method is available, otherwise the
   fallback shipping method will be used.

.. confval:: de.options.n.available.until

   :Type: Text

   Only if the gross price of the products is less than or equal to the
   specified value, this shipping method is available, otherwise the
   fallback shipping method will be used.

.. confval:: de.options.n.fallBackId

   :Type: Text

   If the shipping method is not available, the shipping method with this
   ID will be used.

Zone configuration
==================

plugin.tx_cart.shippings.zones
------------------------------

If no individual country configuration can be found, it is also possible to
work with zones in the TypoScript.
This saves a lot of configuration work if you want to deliver to many
countries.

.. code-block:: typoscript

   plugin.tx_cart {
       shippings {
           zones {
               1 {
                   preset = 1
                   countries = de,at,ch
                   options {
                       1 {
                           title = Standard
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

.. confval:: n

   :Type: int

   You can configure up to n different zones.

.. confval:: n.countries

   :Type: int

   List of countries for which this configuration is valid.

.. NOTE::
   * The system first searches for a suitable configuration in the list of country configurations.
   * The list of zone configurations is then looked through. The first matching configuration is used.

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

   FlexPrices/Index
