.. include:: ../Includes.rst.txt
.. _quick_start:

===========
Quick start
===========

Watch the video and/or read the instructions to get a (minimal) working
shop setup.

.. youtube:: lAu7hlRmZP8

Instructions
============

.. rst-class:: bignums

#. Install this extension:

   See :ref:`Installation → Install the extension <installation_extension>`.

#. Include the TypoScript of this extension:

   See :ref:`Installation → Include TypoScript <installation_typoscript>`.

#. Install a product database extension

   You need to install at least one more extension to get a working shop setup.
   This other extension will offer the products itself. This can be one of the
   existing extensions or a custom extension.

   See :ref:`other_cart_extensions`.

   The next steps of this quick start use `EXT:cart_products`.
   Include the TypoScript of this second extension as described above or as
   described in the corresponding documentation.

#. Create the needed pages and directories in the TYPO3 backend.

   .. figure:: ../Images/QuickStart/Page-Tree.png
      :width: 350
      :alt: Page setup for a minimal shop
      :class: with-shadow my-3

   #. Create a page for cart. Here it's named *Cart*.
   #. Create a page for a list of products. Here it's named *Products*.
   #. Create a page for the detail view of a product. Here it's named *Detail*.
   #. Create a page of type "directory" where the products will be stored.
      Here it's named *Products Storage*.
   #. Create a page of type "directory" where the orders will be stored.
      Here it's named *Orders*.

   .. TIP::
      You can create multiple, even nested, directories to organize your
      products in the backend.

      In this case you can even have multiple pages for lists and detail views.

#. Create Plugin for the cart

   .. figure:: ../Images/QuickStart/Plugin-Cart-Cart.png
      :width: 800
      :alt: Settings within the plugin "Cart: Cart"
      :class: with-shadow my-3

   On the page *Cart*:

   - Create a record of type plugin *Cart: Cart*.

#. Create Plugin for the list view

   .. figure:: ../Images/QuickStart/Plugin-Cart-Products-List.png
      :width: 800
      :alt: Settings within the plugin "Cart: Products" for the list view
      :class: with-shadow my-3

   On the page *Products*:

   - Create a record of type plugin *Cart: Products* and make the
     following settings:

     - Choose "Display Type" = "List (and Show) View".
     - In *Product Detail Page* choose the page *Detail*.
     - In *Record Storage Page* choose the page *Products Storage*.

#. Create Plugin for the detail view

   .. figure:: ../Images/QuickStart/Plugin-Cart-Products-Detail.png
      :width: 800
      :alt: Settings within the plugin "Cart: Products" for the detail view
      :class: with-shadow my-3

   On the page *Detail*:

   - Create a record of type plugin *Cart: Products*.

     - Choose "Display Type" = "Show View".


#. Set TypoScript constants

   The following can be done in the TYPO3 backend in the TypoScript Module
   (as shown in the video) or better in your own sitepackage (which is
   preferred because it is under source control).

   See also :ref:`Base Configuration <base>`.

   .. code-block:: typoscript
      :caption: EXT:sitepackage/Configuration/TypoScript/constants.typoscript

      plugin.tx_cart.settings {
          cart {
              pid = # The PID of the page "Cart".
              isNetCart = # 0 or 1, depending on your needs.
          }
          order.pid = # The PID of the page "Orders".
      }

#. Set needed TypoScript setup

   You should set in your
   `sitepackage/Configuration/TypoScript/setup.typoscript` fitting
   values for:

   - :ref:`email <mail_addresses>`
   - :ref:`payments <payment_methods>`
   - :ref:`shippings <shipping_methods>`
   - :ref:`currencies <currency>` (only needed when not only using €),

#. Link for consent checkboxes in the cart

   .. code-block:: typoscript
      :caption: EXT:sitepackage/Configuration/TypoScript/constants.typoscript

      plugin.tx_cart.settings.cart {
          acceptTermsAndConditions.pid = # The PID of your terms and conditions page.
          acceptRevocationInstruction.pid = # The PID of your revocation instructions page.
          acceptPrivacyPolicy.pid = # The PID of your privacy policy page
      }

#. Create a product

   .. figure:: ../Images/QuickStart/New-Product-01.png
      :width: 800
      :alt: Create a new record
      :class: with-shadow my-3

   .. figure:: ../Images/QuickStart/New-Product-02.png
      :width: 800
      :alt: Create a new product
      :class: with-shadow my-3

   - Go to the page *Product Storage*, switch to the backend module "List".
   - Click on :guilabel:`+ Create a new record` → :guilabel:`Product` for a
     product which is given from the database extension (which is
     `EXT:cart_products` in this quick start).
   - Fill in necessary product data and save.

#. Done

   You should now be able to see the product in the frontend and make an order.
