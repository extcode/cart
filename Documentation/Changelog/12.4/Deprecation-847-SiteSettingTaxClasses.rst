.. include:: ../../Includes.rst.txt

===================================================
Deprecation: #847 - Site setting extcode.taxclasses
===================================================

See `Issue 847 <https://github.com/extcode/cart/issues/847>`__

Description
===========

The site sets loader for tax classes
``\Extcode\Cart\Configuration\Loader\SiteSets\TaxClassLoader`` read the site
setting ``extcode.taxclasses``, but the site set ``extcode/cart-taxclasses``
provided ``extcode.cart-taxclasses``. With the set no tax class was found.

Site settings of cart use the namespace ``extcode.cart`` with the keys of the
TypoScript, e.g. ``plugin.tx_cart.taxClasses`` becomes
``extcode.cart.taxClasses``. The set provides the tax classes as
``extcode.cart.taxClasses`` and the loader reads them from there:

.. code-block:: yaml
   :caption: config/sites/<site>/settings.yaml

   extcode:
     cart:
       taxClasses:
         1:
           value: 19
           calc: 0.19
           name: 'normal'

The tax classes are sorted by their key, and typed values of the site
settings, e.g. ``value: 19``, are supported.

Impact
======

The site setting ``extcode.taxclasses`` is still read if
``extcode.cart.taxClasses`` is not set, and triggers a deprecation. It will
stop working in cart 13.0.

Affected Installations
======================

All installations which use the site sets loader for tax classes with an own
site set or own site settings providing ``extcode.taxclasses``.

Migration
=========

Rename ``extcode.taxclasses`` to ``extcode.cart.taxClasses`` in the site set
or the site settings.

.. index:: Frontend, PHP-API
