.. include:: ../../Includes.txt

===================================
Feature: 390 - Add Address Addition
===================================

See :issue:`390`

Description
===========

Some customers have additional address data, which enables the supplier to deliver the shipment. To enable this directly, the new database field 'addition' for addresses was created and built into the partial.
However, it is validated to Empty by default and is not output automatically. If you want to use it, you have to configure it via TypoScript.
For more information check out the :ref:`validation` documentation page.

Impact
======

No Impact.

Database compare required
=========================

The Feature adds a new column to the database. A database compare is required to add this new field.
