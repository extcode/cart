.. include:: ../../../Includes.txt

===============
Payment methods
===============

The payment methods are defined via TypoScript for each shopping cart.
The standard template already includes a payment method (prepayment).

With the definition plugin.tx_cart.settings.allowedCountries the output of
the selector in the shopping cart is defined. The options can also be
translated by an own SelectViewhelper.

.. important::
   The provided TypoScript of the extension provides a configuration and
   translation for the German-speaking area.

The parameter plugin.tx_cart.settings.defaultCountry defines which country
should be preselected.

.. important::
   If different payment methods are defined for different countries, the
   selection of the invoice address is used for the permitted payment methods.
   The country of the shipping address, even if a different shipping address
   was specified, is not used.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.free.from
   Data type
      float
   Description
      If the gross price of the products is greater than or equal to the
      specified value, the price for the payment method is 0.00.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.free.until
   Data type
      float
   Description
      If the gross price of the products is less than or equal to the
      specified value, the price for the payment method is 0.00.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.fallBackId
   Data type
      int
   Description
      If the payment method is not available, the payment method with
      this ID is used.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.available.from
   Data type
      float
   Description
      This payment method is only available if the gross price of the products
      is greater than or equal to the specified value, otherwise the fallback
      payment method is used.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.available.until
   Data type
      float
   Description
      This payment method is only available if the gross price of the products
      is less than or equal to the specified value, otherwise the fallback
      payment method is used.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.redirects.success.url
   Data type
      Text
   Description
      If the redirect URL is configured for the payment method used for an
      order, the system redirects to the specified URL after a successful
      order instead of displaying the confirmation page.

Zone configuration
==================

If no individual country configuration can be found, it is also possible to
work with zones in the TypoScript.
This saves a lot of configuration work if you want to deliver to many
countries.

.. code-block:: typoscript

   plugin.tx_cart {
       payments {
           zones {
               1 {
                   preset = 1
                   countries = de,at,ch
                   options {
                       1 {
                           title = Vorkasse
                           extra = 0.00
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
      plugin.tx_cart.payments.zones.n
   Data type
      int
   Description
      You can configure up to n different zones.

.. container:: table-row

   Property
      plugin.tx_cart.payments.zones.n.countries
   Data type
      int
   Description
      List of countries for which this configuration is valid.

.. NOTE::
   * The system first searches for a suitable configuration in the list of country configurations.
   * The list of zone configurations is then looked through. The first matching configuration is used.

Deactivate payment methods
==========================

At the moment, the existence of a payment method is required for the processing
of an order. That means that at least one payment method must be specified.
In this case the selection block for the payment can be hidden.

::

   plugin.tx_cart {
        settings {
            showCartAction {
                showPartials {
                   paymentMethodForm = false
                }
            }
        }
   }

.. container:: table-row

   Property
      plugin.tx_cart.settings.showCartAction.showPartials.paymentMethodForm
   Data type
      boolean
   Description
      Enables/disables the display and selection of configured payment methods
      in the shopping cart.
   Default
      true

.. toctree::
   :maxdepth: 5
   :titlesonly:

   MainConfiguration/Index
