.. include:: ../../Includes.rst.txt

11.9 Changes
============

**Table of contents**

.. contents::
   :local:
   :depth: 1

11.9.3
------

Bugfixes
~~~~~~~~

*  Redirect to the success URL of the payment method after an order (`#854 <https://github.com/extcode/cart/pull/854>`__)
*  Send the order emails if the payment method is not configured (`#856 <https://github.com/extcode/cart/pull/856>`__)
*  Subtract coupon taxes only once in Cart::getTotalTaxes() (`#860 <https://github.com/extcode/cart/pull/860>`__)
*  Sum up taxes of tax classes without products (`#860 <https://github.com/extcode/cart/pull/860>`__)

11.9.2
------

Bugfixes
~~~~~~~~

*  Keep invoice number in backend order filter (`#809 <https://github.com/extcode/cart/pull/809>`__)
*  Find template of PaymentPaidShippingOpen widget (`#813 <https://github.com/extcode/cart/pull/813>`__)
*  Respect status filter in OrdersPerDayProvider (`#813 <https://github.com/extcode/cart/pull/813>`__)
*  Return turnover as float in TurnoverPerDayProvider (`#813 <https://github.com/extcode/cart/pull/813>`__)
*  Build a new query for each call of OrderItemsProvider (`#813 <https://github.com/extcode/cart/pull/813>`__)
*  Show the searched shipping status in the order list (`#821 <https://github.com/extcode/cart/pull/821>`__)

11.9.1
------

Bugfixes
~~~~~~~~

*  Generate backend documents with cart_pdf 8 (`#807 <https://github.com/extcode/cart/pull/807>`__)

11.9.0
------

Features
~~~~~~~~

*  :doc:`#803 - Add event to export orders in backend module <Feature-803-AddEventToExportOrdersInBackendModule>`

Deprecations
~~~~~~~~~~~~

*  :doc:`#803 - Order export in backend module <Deprecation-803-OrderExportInBackendModule>`

Bugfixes
~~~~~~~~

*  Respect search of order list in CSV export (`#805 <https://github.com/extcode/cart/pull/805>`__)

.. toctree::
   :hidden:
   :glob:

   *-*
