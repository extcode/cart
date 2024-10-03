.. include:: ../../Includes.rst.txt

=====================================
Feature: #465 - Updated backend views
=====================================

See `Issue 465 <https://github.com/extcode/cart/issues/465>`__

Description
===========

The view of the backend module were refactored. This has the following benefits:

* No inline CSS which avoid problems with Content-Security-Policies.
* Clearer design.
* Responsive design (old design was not usable on smartphones).
* Order tables with colors to improve the visibility of status of orders.
* Backend CSS reduced / updated to really used HTML.

Impact
======

Own overwrites might need adaption otherwise this change should not have any
negative impact.


.. index:: Backend
