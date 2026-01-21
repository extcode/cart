.. include:: ../../../Includes.rst.txt

.. _payment_methods:

===============
Payment methods
===============

The payment methods are defined via TypoScript for each shopping cart.
The standard template already includes a payment method (prepayment) as
shown below. In case the payment method(s) for several countries shall
be configured, please also check the possibility to use a 'zone' based 
configuration. This reduces the amount of configuration necessary.

With the definition `plugin.tx_cart.settings.allowedCountries` the output of
the selector in the shopping cart is defined. The options can also be
translated by an own `SelectViewhelper`.

.. important::
   If different payment methods are defined for different countries, the
   selection of the invoice address is used for the permitted payment methods.

   The country of the shipping address, even if a different shipping address
   was specified, is not used.

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

plugin.tx_cart.settings
-----------------------

.. confval:: countries.preset

   :Type: int
   :Default: 1

   Defines which country will be preselected.


plugin.tx_cart.payments.countries
---------------------------------

.. confval:: payments.countries.<country-code>.preset

   :Type: int

   Defines which payment method is selected by default if the user has not yet
   selected a different payment method.

   If the payment method is not defined when changing the country of account,
   the payment method defined here for the country of invoice will also be
   selected.

.. confval:: payments.countries.<country-code>.options.<n>

   :Type: array
   :Default: options.1

   You can configure n different payment methods.

.. confval:: payments.countries.<country-code>.options.<n>.title

   :Type: string

   Name of the payment method (e.g.: prepayment, cash on delivery).

.. confval:: payments.countries.<country-code>.options.<n>.extra

   :Type: float
   :Default: 0.00

   Costs for the payment method to be billed to the customer (for example, 1.50).
   The currency depends on the standard configuration.

.. confval:: payments.countries.<country-code>.options.<n>.taxClassId

   :Type: int

   ID of the tax class for this payment method. The taxClassId must either be
   assignable to a defined tax class.

   However, the values `-1` and `-2` are also allowed here. This is a feature
   which was introduced for the calculation of shipping costs. Nonetheless it
   can also be used for payments

   * `-1` → The products within the shopping cart are taken. Of those products
     the tax class with the highest value is then taken as tax class for the
     calculation of the payment method costs.
   * `-2` → The products within the shopping cart are taken. The tax for the
     payment method costs is calculated as a percentage of the tax of those
     products.

.. confval:: payments.countries.<country-code>.options.<n>.status

   :Type: string

   The status that the order with this payment method should have by default.

Country configuration
=====================

plugin.tx_cart.payments.countries
---------------------------------

.. confval:: payments.countries.<country-code>.options.<n>.free.from

   :Type: float

   If the gross price of the products is greater than or equal to the
   specified value, the price for the payment method is 0.00.

.. confval:: payments.countries.<country-code>.options.<n>.free.until

   :Type: float

   If the gross price of the products is less than or equal to the
   specified value, the price for the payment method is 0.00.

.. confval:: payments.countries.<country-code>.options.<n>.fallBackId

   :Type: int

   If the payment method is not available, the payment method with
   this ID is used.

.. confval:: payments.countries.<country-code>.options.<n>.available.from

   :Type: float

   This payment method is only available if the gross price of the products
   is greater than or equal to the specified value, otherwise the fallback
   payment method is used.

.. confval:: payments.countries.<country-code>.options.<n>.available.until

   :Type: float

   This payment method is only available if the gross price of the products
   is less than or equal to the specified value, otherwise the fallback
   payment method is used.

.. confval:: payments.countries.<country-code>.options.<n>.redirects.success.url

   :Type: string

   If the redirect URL is configured for the payment method used for an
   order, the system redirects to the specified URL after a successful
   order instead of displaying the confirmation page.

Zone configuration
==================

plugin.tx_cart.payments.zones
-----------------------------

The configuration of payment zones can be used to ease the configuration
of payments for multiple countries. Their usage saves a lot of configuration
work, if you want to deliver to many countries.
In case both country configuration as well as zone configuration are valid for
a dedicated country, the country configuration has precedence over the zone configuration. 

.. NOTE::
   * The system first searches for a suitable configuration in the list of country configurations.
   * The list of zone configurations is then looked through. The first matching configuration is used.
   * The country-based payment configuration coming with the extension can interfere with your zone configuration for payments. 
     Therefore, check if you need to remove the country-based configuration when using zones as shown below.

.. code-block:: typoscript
   :caption: Can be set in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       payments {
           // this removes any previous country based payment configuration
           countries >       
           zones {
               1 {
                   preset = 2
                   countries = de,at,ch
                   options {
                       1 {
                           title = Vorkasse
                           extra = 0.00
                           taxClassId = 1
                           status = open
                       }
                       2 {
                           provider = PAYPAL
                           title = Paypal
                           extra = 0.50
                           taxClassId = 1
                           status = open
                         }                       
                    }
               }
           }
       }
   }

.. confval:: payments.zones.<n>

   :Type: int

   You can configure up to n different zones.

.. confval:: payments.zones.<n>.countries

   :Type: int

   List of countries for which this configuration is valid.

Deactivate payment methods
==========================

At the moment, the existence of a payment method is required for the processing
of an order. That means that at least one payment method must be specified.
In this case the selection block for the payment can be hidden.

.. code-block:: typoscript
   :caption: Can be set in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
        settings {
            showCartAction {
                showPartials {
                   paymentMethodForm = false
                }
            }
        }
   }

See :ref:`plugin.tx_cart.settings.showCartAction.showPartials.paymentMethodForm <plugin_tx_cart_settings_showCartAction_showPartials_paymentMethodForm>`

