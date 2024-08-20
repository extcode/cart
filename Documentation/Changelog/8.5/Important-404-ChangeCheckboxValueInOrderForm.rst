.. include:: ../../Includes.rst.txt

===================================================
Breaking: #404 - Change Checkbox Value in OrderForm
===================================================

See :issue:`360`

Description
===========

In the `OrderForm.html` partial the the value of the checkbox should not depend on the shippingSameAsBilling property
of the shopping cart.
This property should only be responsible for the checkbox.

Affected Installations
======================

This affects all installations overriding the `OrderForm.html`.

Migration
=========

Replace the fluid condition of the value attribute with the value 1.

.. index:: Template, Frontend
