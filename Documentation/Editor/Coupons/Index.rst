.. include:: ../../Includes.txt

=================
Coupons / Voucher
=================

At the moment exists two types of coupons: "fix" and "percentage".
They do what their names imply:

* The type "fix" reduces the price by a fixed amount, e.g. 20,00 €.
* The type "percentage" reduces the price by a percentage, e.g. 10%.

It is recommended to only use one type within a shop or to not allow to combine
coupons.

.. TIP::
   To allow vouchers in the cart you have enter the pages which contain the
   vouchers in the "Record Storage Page" of the plugin.

Known Problems
==============

* Creating a voucher of type "percentage" requires at the moment the definition
  of a tax class although this is not logical. That has historical reasons, see
  :ref:`Feature 392 Add Percentage Discount <feature_392>`.
* There might be bugs if the fixed discount is bigger than the cart amount.
