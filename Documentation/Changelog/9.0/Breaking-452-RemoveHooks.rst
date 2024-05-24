.. include:: ../../Includes.txt

=============================
Breaking: #452 - Remove Hooks
=============================

See :issue:`452`

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
