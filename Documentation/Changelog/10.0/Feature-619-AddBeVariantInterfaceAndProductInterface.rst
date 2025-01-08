.. include:: ../../Includes.rst.txt

===========================================================
Feature: #619 - Add BeVariantInterface and ProductInterface
===========================================================

See `Issue 619 <https://github.com/extcode/cart/issues/619>`__

Description
===========

To make the extension even more flexible, only the interfaces for `Products` and
`BeVariants` are to be used in the shopping cart. This makes it easier to replace
them with your own implementations of the interfaces.
Furthermore, for `Products`, `BeVariants` and `FeVariants` corresponding
Factories and FactoryInterfaces should offer the possibility to customize the
products in the shopping cart even easier with own Factories.

Impact
======

No direct impact.
XCLASSing for products, BeVariants and FeVariants should no longer be necessary.
Instead, separate classes should implement the corresponding interfaces.

.. index:: Backend, API
