.. include:: ../../Includes.rst.txt

=====================================================================
Feature: #895 - Shippable, cancelable and returnable ordered products
=====================================================================

See `Issue 895 <https://github.com/extcode/cart/issues/895>`__

Description
===========

An ordered product (``tx_cart_domain_model_order_product``) stores whether it
is shippable, cancelable and returnable in the new fields ``shippable``,
``cancelable`` and ``returnable``. The values are set once when the order is
persisted and are not changed afterwards, even if the product changes later.
The model ``\Extcode\Cart\Domain\Model\Order\Product`` provides them with
``isShippable()``, ``isCancelable()`` and ``isReturnable()``.

Defaults:

*  ``shippable``: ``true`` unless the product in the cart is a virtual product
   (``ProductInterface::isVirtualProduct()``).
*  ``cancelable`` and ``returnable``: ``true``.

The new event ``\Extcode\Cart\Event\Order\BeforePersistOrderProductEvent``
is dispatched once per ordered product (or backend variant) before it is
stored. Product extensions can change the values with ``setShippable()``,
``setCancelable()`` and ``setReturnable()``, e.g. for personalized products
that cannot be returned.

The order details in the backend module mark ordered products that are not
shippable, not cancelable or not returnable.

The fields are the base for partial deliveries, cancellations and returns per
ordered product. Rules that depend on the time (e.g. a ticket can be canceled
until 14 days before the event) do not belong into these fields.

Impact
======

The database schema has to be updated. Products of existing orders are
shippable, cancelable and returnable (default of the database fields).

.. index:: Backend, Database, PHP-API
