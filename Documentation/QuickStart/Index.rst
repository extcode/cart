.. include:: ../Includes.txt
.. _quick_start:

===========
Quick start
===========

Watch the video and/or read the instructions to get a (minimal) working
shop setup.

.. youtube:: lAu7hlRmZP8

Instructions
============

.. rst-class:: bignums-tip

#. Install this extension:

   See :ref:`Installation → Install the extension <installation_extension>`.

#. Include the TypoScript of this extension:

   See :ref:`Installation → Include TypoScript <installation_typoscript>`.

#. Create a page for the cart.

   - Create in the TYPO3 backend a page for the
     :ref:`cart plugin <plugin_cart_cart>` where the customer will check-out.
   - Add the plugin "Cart: Cart" on this page.

#. Create a storage for the orders

   Create a page record of type "directory" where the orders will be stored.
   This can have the name "Orders".

#. Set TypoScript constants

   The following can be done in the TYPO3 backend in the TypoScript Module
   or better in your own sitepackage (which is preferred because it is under
   source control).

   .. code-block:: typoscript
      :caption: EXT:sitepackage/Configuration/TypoScript/constants.typoscript

      plugin.tx_cart.settings {
          cart {
              pid = # The PID of the page of step 3.
              isNetCart = # 0 or 1, depending on your needs.
          }
          order.pid = # The PID of the page of step 4.
      }

#. Install a product database extension

   As described in :ref: `Installation <installation>`__ you need to install
   another extension which offer the products itself. This can be one of the
   existing extensions or a custom extension.

   The next steps of this quick start use `EXT:cart_products` as example.
   Include the TypoScript of this second extension as described above / as
   described in the corresponding documentation.

#. Set needed TypoScript setup

   Set in your `sitepackage/Configuration/TypoScript/setup.typoscript` fitting
   values for at least
   :ref:`email<adminstration_order_mail>`,
   :ref:`currencies<adminstration_currencytranslationsconfiguration>`,
   :ref:`payments<adminstration_paymentmethods_mainconfiguration>` and
   :ref:`shippings<adminstration_shippingmethods_mainconfiguration>`.

#. Set further TypoScript constants

   .. code-block:: typoscript
      :caption: EXT:sitepackage/Configuration/TypoScript/constants.typoscript

      plugin.tx_cart.settings.cart {
          acceptTermsAndConditions.pid = # The PID of your terms and conditions page.
          acceptRevocationInstruction.pid = # The PID of your revocation instructions page.
          acceptPrivacyPolicy.pid = # The PID of your privacy policy page
      }

#. Create a storage for your products

   Create a page record of type "directory" where the products will be stored.
   You can create multiple, even nested, to organize your products in the
   backend.

#. Create a product

   Go to the page of the previous step, switch to the backend module "List".
   Create a new record for a product which is given from the database extension
   which is `EXT:cart_products` in this tutorial.

#. A page for a list of products

   - Create a page to show a list of products.
   - Create a record of type plugin "Cart: Products", choose
     "Display Type" = "List (and Show) View".
   - Set "Product Detail Page" and "Record Storage Page" accordingly.

#. A page for the product details

   - Create a page to show an individual product.
   - Create a record of type plugin "Cart: Products", choose
     "Display Type" = "Show View"


