.. include:: ../../Includes.txt

===================================================================
Deprecation: 339 - Taxation of Service Cost as an Ancillary Service
===================================================================

See :issue:`339`

Description
===========

In germany and austria the tax for shipping is related to the products in cart.
If you only have products with one tax class you are save, but if you sell books (reduced tax) and shirts (normal tax)
the tax calculation for shipping use both tax classes. Percentage wise, the calculation of the tax is divided.
The new version will add two options to TypoScript for taxClassId configuration.
- `taxClassId = -1` for simple calculation, where the tax rate of shipping is based on the highest tax rate in the shopping cart.
- `taxClassId = -2` option for the more complicated percentage calculation of the tax.

.. NOTE::
   The configuration of the taxClassId with a negative value is only allowed here. No tax class with negative values
   must or may be defined. For compatibility reasons, negative values were inserted at this point.
   This configuration will change in the upcoming version for TYPO3 v11.

Impact
======

No Impact.

Affected Installations
======================

No Installations are infected. There might be some issues on classes extending `\Extcode\Cart\Domain\Model\Cart\Extra`
or `\Extcode\Cart\Domain\Model\Cart\Service`.
