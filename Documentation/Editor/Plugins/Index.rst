.. include:: ../../Includes.rst.txt

=======
Plugins
=======

Plugin "Cart: Cart"
===================

This plugin shows the current status of the cart, the products it contains and
the form to complete the order.

.. NOTE::
   To allow vouchers in the cart you have enter the pages which contain the
   vouchers in the "Record Storage Page" of the plugin.

.. _plugin_cart_mini_cart:

Plugin "Cart: Mini Cart"
========================

This plugin can be included as mini cart and gives a shortened view of the cart.

An option to add this plugin is via TypoScript as shown below:

.. code-block:: typoscript
   :caption: EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   lib.miniCart = COA_INT
   lib.miniCart {
     10 = USER_INT
     10 {
       userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
       extensionName = Cart
       pluginName = MiniCart
       vendorName = Extcode
     }
   }

This TypoScript object can then be used within a Fluid template:

.. code-block:: html
   :caption: e.g. EXT:sitepackage/Resources/Private/Partials/Header.html

   <header>
       <!-- Other elements within your header ... -->
       <f:cObject typoscriptObjectPath="lib.miniCart"/>
   </header>


.. _plugin_cart_orders:

Plugin "Cart: Orders"
=====================

A logged in frontend user can see the orders with the help of this plugin.
The plugin offers a list view and a detail view.

.. NOTE::
   It's necessary to add the page(s) where the orders are stored to the
   "Record Storage Page" in the plugin settings. Otherwise the plugin will not
   work.
