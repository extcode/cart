.. include:: ../../Includes.rst.txt

========================================================
Feature: #818 - Number widgets for orders and turnover
========================================================

See `Issue 818 <https://github.com/extcode/cart/issues/818>`__

Description
===========

The dashboard gets four new widgets in the widget group "Cart":

*  **Orders Today** and **Orders this Month** show the number of orders (new
   `OrdersInPeriodProvider`).
*  **Turnover Today** and **Turnover this Month** show the gross total of the
   orders with currency (new `TurnoverInPeriodProvider`).

All four use the new `NumberWidget`, which shows the number like the
`NumberWithIconWidget` of the dashboard without repeating the title of the
widget, and amounts with currency.

Both providers take the options `filter`, `storagePids`, `fieldName` and the
new option `period` (`today`, `week`, `month` or `year`), the turnover
provider also the option `sum`. Like the other widgets they only show orders
in storage folders the backend user may show.

The option `filter` of all providers takes several status now, as list or
comma separated string, e.g. `['payment' => ['status' => ['open', 'paid']]]`
for open and paid invoices. The number widgets leave out orders with the
payment status `canceled` by default.

See :ref:`dashboard_widgets` for all options and an example of an own widget.

Impact
======

The new widgets can be added to dashboards. Existing dashboards are not
changed.

.. index:: Backend
