.. include:: ../../Includes.rst.txt

=============================
Breaking: #452 - Remove Hooks
=============================

See `Issue 452 <https://github.com/extcode/cart/issues/452>`__

Description
===========

The `changeVariantDiscount` Hooks have been removed. Cart will not provide
a replacement.

Affected Installations
======================

All installations that used the hooks to programmatically adjust the behavior
of the extension are affected.

Migration
=========

Override the `BeVariant` class by implementing the `BeVariantInterface`.

.. index:: API
