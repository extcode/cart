.. include:: ../../Includes.rst.txt

12.3 Changes
============

**Table of contents**

.. contents::
   :local:
   :depth: 1

12.3.0
------

Features
~~~~~~~~

*  :doc:`#815 - Configurable data providers of the widgets <Feature-815-ConfigurableDashboardWidgetProviders>`
*  :doc:`#818 - Number widgets for orders and turnover <Feature-818-DashboardNumberWidgets>`
*  :doc:`#819 - Latest orders widget links to the order list <Feature-819-LatestOrdersWidgetLinksToOrderList>`

Bugfixes
~~~~~~~~

*  Keep invoice number in backend order filter (`#808 <https://github.com/extcode/cart/pull/808>`__)
*  Find template of PaymentPaidShippingOpen widget (`#812 <https://github.com/extcode/cart/pull/812>`__)
*  Respect status filter in OrdersPerDayProvider (`#812 <https://github.com/extcode/cart/pull/812>`__)
*  Return turnover as float in TurnoverPerDayProvider (`#812 <https://github.com/extcode/cart/pull/812>`__)
*  Build a new query for each call of OrderItemsProvider (`#812 <https://github.com/extcode/cart/pull/812>`__)
*  Remove duplicate dashboard widget title caused by outdated widget layout (`#748 <https://github.com/extcode/cart/pull/748>`__)
*  Leave out canceled orders in the turnover per day widget (`#817 <https://github.com/extcode/cart/pull/817>`__)
*  Show the searched shipping status in the order list (`#820 <https://github.com/extcode/cart/pull/820>`__)
*  Show the order number on the order confirmation pages (`#825 <https://github.com/extcode/cart/pull/825>`__)

.. toctree::
   :hidden:
   :glob:

   *-*
