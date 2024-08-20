.. include:: ../../Includes.rst.txt
    
.. _feature_392:

======================================
Feature: 392 - Add Percentage Discount
======================================

See :issue:`392`

Description
===========

An important feature, which was frequently requested, are coupons that allow a
percentage discount.
Now there is a first implementation. This introduces a new voucher type.
Percentage vouchers are currently not combinable with other vouchers on the
shopping cart, because with the currently available fixed voucher values,
the order of calculation would be crucial.

Impact
======

The current implementation still requires the configuration of the tax class.
This makes sense for vouchers with a fixed amount. For percentage discount
vouchers, the tax is of course also calculated on a percentage basis.
Nevertheless, a tax class must be selected in the backend, which is then
ignored in the calculation.

Only with the next major release for TYPO3 v12 and v11, the breaking change can
be made here and the tax class becomes an optional specification and can then
also be handled accordingly in the TCA.

If only percentage vouchers are used in the store, there is the possibility
to hide and preset the fields accordingly in the backend.
In stores, in which there should be both types of vouchers, the editors who can
create vouchers, are to be trained.

Sponsors
========

`Liebman Design Import e.K. <https://www.liebman-design-import.com>`_ would like to offer customers percentage vouchers on
toys after relaunching the shop with TYPO3 v11.

`SEITENFORMATE, Agentur für Kommunikationsdesign <https://seitenformate.de/>`_ would like to implement percentage
discount vouchers for an event booking shop for special registered customers.
