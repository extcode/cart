.. include:: ../../Includes.rst.txt

================================================================
Feature: #896 - Taxes per tax class of the discounts of an order
================================================================

See `Issue 896 <https://github.com/extcode/cart/issues/896>`__

Description
===========

A percentage coupon reduces the taxes of all tax classes of the cart, but the
discount of the order (``tx_cart_domain_model_order_discount``) stored only
the tax of the coupon in its own tax class, which is ``0.0`` for percentage
coupons. The tax class of the discount was not stored at all. The totals of
the order were correct.

The discount of an order now stores:

``taxes``
   The taxes of the discount per tax class of the order as JSON, the uid of
   the tax class of the order (``tx_cart_domain_model_order_taxclass``) is the
   key, e.g. ``{"1":0.38,"2":0.07}``.

``tax_class``
   The tax class of the order the coupon is configured with.

``coupon_type``
   The class of the coupon in the cart, e.g.
   ``Extcode\Cart\Domain\Model\Cart\CartCouponPercentage``.

The model ``\Extcode\Cart\Domain\Model\Order\Discount`` provides them with
``getTaxes()``, ``getTaxClass()`` and ``getCouponType()``. ``hasTaxes()``
returns ``false`` if the taxes per tax class are unknown. The constructor got
the optional parameters ``$couponType`` and ``$taxes``; the discount has no
setters, it is not changed after the order was persisted.

The order views in the backend and in the frontend show the taxes of a
discount in the columns of their tax classes. The frontend view shows the
discounts of the order, too.

Impact
======

Run the database analyzer to add the columns and the upgrade wizard
"Cart: Store the taxes per tax class of the discounts of orders"
(``extcodeCartOrderDiscountTaxes``) for existing orders.

The upgrade wizard calculates the taxes of the discounts per tax class from
the stored taxes of the order: tax of the products plus tax of shipping and
payment minus total tax. If an order has several discounts, the taxes are
split by their gross. It also stores the tax class of fixed coupons and the
coupon type, if a coupon with the same code still exists.

The upgrade wizard skips and lists orders whose numbers do not add up, e.g.
orders with special services, whose prices are not stored in the order, or
orders with fixed coupons of different tax classes. The taxes per tax class
of their discounts stay unknown and the tax columns of the discounts stay
empty.

.. index:: Database, Frontend, Backend
