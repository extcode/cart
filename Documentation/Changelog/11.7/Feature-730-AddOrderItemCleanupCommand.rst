.. include:: ../../Includes.rst.txt

==============================================
Feature: #730 - Add order item cleanup command
==============================================

See `Issue 730 <https://github.com/extcode/cart/issues/638>`__

Description
===========

After a while, more and more orders will clutter up the database. Depending on
the shop, it may no longer be possible to delete them manually.

A cleanup command should ensure that older orders created before a certain date
can be deleted automatically.

Among other things, this serves to conserve data. As a rule, older orders are no
longer needed on the website for further processing, unless you save the orders
for a front-end user and want to keep them available in the account for a long
time.

The first version should:

- use the DataHandler to set orders to be deleted
- ignore special cases such as those associated with a frontend user
- have no interaction with the user of the command
- not allow execution via the scheduler

The user of the command should create a backup of the database beforehand.
The database must be cleaned up afterwards using the
`vendor/bin/typo3 cleanup:deletedrecords`, as the command only sets orders
deleted flag to true, but does not remove them.

Impact
======

No direct impact.

.. index:: API
