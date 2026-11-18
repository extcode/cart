.. include:: ../../Includes.rst.txt

==========================================================
Feature: #815 - Configurable data providers of the widgets
==========================================================

See `Issue 815 <https://github.com/extcode/cart/issues/815>`__

Description
===========

The data providers of the dashboard widgets share the options `filter`
(status of payment and shipping) and the new option `storagePids` (storage
folders of the orders). The `TurnoverPerDayProvider` respects the `filter` as
well now, e.g. to sum up only paid orders.

The widgets only show orders in storage folders the backend user may show
(page permissions and mount points). Admins see all orders as before.

The chart providers `OrdersPerDayProvider` and `TurnoverPerDayProvider` fetch
the orders of the whole period with one query instead of one query per day.

See :ref:`dashboard_widgets` for all options.

Impact
======

Backend users without admin rights only see orders in storage folders they
may show. Grant them access to the storage folders of the orders if they
should see all orders in the widgets.

The default of the option `sum` of the `TurnoverPerDayProvider` is
`total_gross` now. The former default `gross` was no field of the order.

The widget "Turnover per Day" leaves out orders with the payment status
`canceled` now. A payment is canceled e.g. if the customer aborts the payment
at the payment provider. Set the option `filter` to get the former behavior.

.. index:: Backend
