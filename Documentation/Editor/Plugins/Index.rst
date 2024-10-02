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

For an example of an implementation see :ref:`Integrate a MiniCart <minicart>`.

.. _plugin_cart_orders:

Plugin "Cart: Orders"
=====================

A logged in frontend user can see the orders with the help of this plugin.
The plugin offers a list view and a detail view.

.. NOTE::
   It's necessary to add the page(s) where the orders are stored to the
   "Record Storage Page" in the plugin settings. Otherwise the plugin will not
   work.
