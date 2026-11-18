.. include:: ../../Includes.rst.txt

================================================================
Feature: #819 - Latest orders widget links to the order list
================================================================

See `Issue 819 <https://github.com/extcode/cart/issues/819>`__

Description
===========

The widget "Latest Orders (paid, not shipped)" uses the table layout of the
dashboard, shows the total of each order and a message if there are no
orders.

If the backend user may show only one storage folder with orders, which is
the usual case, the new button "Show all orders" opens the order list of this
folder, searched for paid but not shipped orders. The button is configured as
service `extcode.cart.button.payment_paid_shipping_open` with the new
`OrderListButtonProvider`.

See :ref:`dashboard_widgets` for the options.

Impact
======

The constructor of the widget class `PaymentPaidShippingOpen` takes an
optional button provider as last argument.

.. index:: Backend
