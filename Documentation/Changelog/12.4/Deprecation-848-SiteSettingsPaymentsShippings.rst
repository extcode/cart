.. include:: ../../Includes.rst.txt

=====================================================================
Deprecation: #848 - Site settings extcode.cart-payments and shippings
=====================================================================

See `Issue 848 <https://github.com/extcode/cart/issues/848>`__

Description
===========

Site settings of cart use the namespace ``extcode.cart`` with the keys of the
TypoScript, see `Issue 847 <https://github.com/extcode/cart/issues/847>`__.
``extcode.cart-*`` is left to extensions of cart, e.g. cart-products.

The site sets ``extcode/cart-payments`` and ``extcode/cart-shippings`` provide
the payment and shipping methods as ``extcode.cart.payments`` and
``extcode.cart.shippings``, matching ``plugin.tx_cart.payments`` and
``plugin.tx_cart.shippings``. The site sets loaders
``\Extcode\Cart\Configuration\Loader\SiteSets\PaymentMethodsLoader`` and
``\Extcode\Cart\Configuration\Loader\SiteSets\ShippingMethodsLoader`` read them
from there:

.. code-block:: yaml
   :caption: config/sites/<site>/settings.yaml

   extcode:
     cart:
       payments:
         countries:
           de: &de
             preset: 1
             options:
               1:
                 title: 'Prepayment'
                 extra: 0.0
                 taxClassId: 1
                 status: 'open'
           at: *de

Use a YAML anchor and aliases to use the configuration of a country for other
countries, the TypoScript copy syntax ``at: .de`` does not work in YAML.

Impact
======

If the site has no settings in ``extcode.cart.payments`` or
``extcode.cart.shippings``, the loaders read ``extcode.cart-payments`` or
``extcode.cart-shippings`` and trigger a deprecation. The settings of both
keys are not merged. The fallback will stop working in cart 13.0.

Affected Installations
======================

All installations which use the site sets loaders for payment or shipping
methods with an own site set or own site settings providing
``extcode.cart-payments`` or ``extcode.cart-shippings``.

Migration
=========

Move ``extcode.cart-payments`` to ``extcode.cart.payments`` and
``extcode.cart-shippings`` to ``extcode.cart.shippings`` in the site set or the
site settings.

.. index:: Frontend, PHP-API
