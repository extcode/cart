.. include:: ../../Includes.rst.txt

=====================================================================
Deprecation: #896 - Tax class of the cart in the discount of an order
=====================================================================

See `Issue 896 <https://github.com/extcode/cart/issues/896>`__

Description
===========

The constructor of ``\Extcode\Cart\Domain\Model\Order\Discount`` expected the
tax class of the cart (``\Extcode\Cart\Domain\Model\Cart\TaxClass``), which
was never stored. It now expects the tax class of the order
(``\Extcode\Cart\Domain\Model\Order\TaxClass``) or ``null``, and
``getTaxClass()`` returns the tax class of the order.

The column ``tax_class_id`` of ``tx_cart_domain_model_order_discount`` is
deprecated. It always contained ``1`` and is replaced by ``tax_class``, which
references the tax class of the order.

Impact
======

Passing the tax class of the cart logs a deprecation, the discount gets no tax
class. It will stop working in cart 13.0.

The column ``tax_class_id`` will be removed in cart 13.0.

Affected Installations
======================

Installations with extensions which create discounts of an order themselves,
e.g. with an own event listener of the ``PersistOrderEvent`` instead of
``\Extcode\Cart\EventListener\Order\Create\PersistOrder\Coupons``.

Installations which read the column ``tax_class_id`` of the discounts of an
order, e.g. in exports or reports.

Migration
=========

Read the tax class with ``getTaxClass()`` or from the column ``tax_class``
instead of ``tax_class_id``.

Pass the tax class of the order. The listeners of the ``PersistOrderEvent``
get them from ``$event->getTaxClasses()`` with the id of the tax class of the
cart as key:

.. code-block:: php

   $orderDiscount = new Discount(
       $cartCoupon->getTitle(),
       $cartCoupon->getCode(),
       $cartCoupon->getGross(),
       $cartCoupon->getNet(),
       $event->getTaxClasses()[$cartCoupon->getTaxClass()->getId()] ?? null,
       $cartCoupon->getTax(),
       $cartCoupon::class,
       $taxesPerTaxClassOfTheOrder
   );

The parameters ``$couponType`` and ``$taxes`` are optional, see
:doc:`Feature-OrderDiscountTaxesPerTaxClass`.

.. index:: PHP-API
