.. include:: ../../../Includes.txt

.. _base:

====
Base
====



.. code-block:: typoscript
   :caption: EXT:sitepackage/Configuration/TypoScript/constants.typoscript

   plugin.tx_cart {
       settings {
           cart {
               pid =
               isNetCart =
           }
           order {
               pid =
           }

           itemsPerPage =
       }
   }

plugin.tx_cart.settings
=======================

.. confval:: cart.pid

   :Required: true
   :Type: string
   Defines the page where the cart plugin is located.

   This is needed to put products in the right shopping cart.

   If settings.addToCartByAjax isn't set, the add to cart action will forwards
   the user to this page.

.. confval:: cart.isNetCart

   :Required: false
   :Type: boolean
   :Default: false

   Defines whether the shopping cart should be treated as a net shopping cart.

   If the shopping cart is a net shopping cart, the price calculations
   are all carried out and displayed on the net prices of the products,
   otherwise the calculations are made with the gross prices.

.. confval:: order.pid

   :Required: true
   :Type: string

   Specifies the folder in which the orders should be stored.

.. confval:: itemsPerPage

   :Required: false
   :Type: int
   :Default: 20 (if there is no TypoScript configuration)

   Defines how many records should be displayed per page in the list action.

   Also valid: `module.tx_cart.settings.itemsPerPage`.
