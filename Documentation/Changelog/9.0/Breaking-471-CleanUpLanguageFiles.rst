.. include:: ../../Includes.rst.txt

===========================================================
Breaking: #471 - Clean up language files
===========================================================

See `Issue 471 <https://github.com/extcode/cart/issues/471>`__

Description
===========

The language files contained a bigger amount of unused entries and their domains
were not really clear. Some entries were renamed to clarify their usage in the
backend.

Affected Installations
======================

Installations which overwrite backend labels might be affected.

Migration
=========

You need to check whether the key of your overwrite was changed and adapt it.

.. index:: Backend
