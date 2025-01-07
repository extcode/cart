.. include:: ../../Includes.rst.txt

=================================================
Breaking: #611 - Change BeVariant Parent Handling
=================================================

See `Issue 611 <https://github.com/extcode/cart/issues/611>`__

Description
===========

Previously, a `BeVariant` had either another `BeVariant` or a `Product` as its parent element. These were passed in the
constructor method.

Thanks to PHP's union types, this can now be resolved. A `BeVariant` now has a `$parent` that is either of type
`BeVariant` or of type `Product`.

The individual methods have been adapted accordingly and it is checked of which class `$parent` is an instance. In some
cases, the case differentiation could be omitted completely.

Affected Installations
======================

All product extensions that use their own `BeVariant` in the products are affected. The provided extensions
extcode/cart-products and extcode/cart-events will be adapted accordingly.

Migration
=========

If a custom product extension is used, the constructor must be adapted accordingly. Furthermore, a few
methods in `BeVariant` have been replaced or their behavior adapted.

.. index:: API
