.. include:: ../../Includes.rst.txt

========================================================================
Deprecation: #879 - Namespaces of the order status and the order history
========================================================================

See `Issue 879 <https://github.com/extcode/cart/issues/879>`__

Description
===========

The classes of the order status and the order history moved from the top level
of :file:`Classes/` into the domain of the order:

====================================  ===============================================
Before                                Now
====================================  ===============================================
``\Extcode\Cart\OrderStatus``         ``\Extcode\Cart\Domain\Order\Status``
``\Extcode\Cart\OrderStatus\Mail``    ``\Extcode\Cart\Domain\Order\Status\Mail``
``\Extcode\Cart\OrderHistory``        ``\Extcode\Cart\Domain\Order\History``
====================================  ===============================================

The class names themselves are unchanged, e.g.
``\Extcode\Cart\OrderStatus\StatusChangeService`` is now
``\Extcode\Cart\Domain\Order\Status\StatusChangeService``.

The old class names are aliases of the new ones. Services which are injected
by their old class name are deprecated aliases in the service container.

Impact
======

The old class names still work. Injecting a service by its old class name
logs a deprecation. The aliases will be removed in cart 13.0.

Affected Installations
======================

All installations with extensions which use classes of the namespaces
``\Extcode\Cart\OrderStatus`` or ``\Extcode\Cart\OrderHistory``, e.g. to
change the payment status of an order with the ``StatusChangeService`` or to
register own status with the constants of ``PaymentStatus`` and
``ShippingStatus`` in :file:`Configuration/OrderStatus.php`.

Migration
=========

Replace ``\Extcode\Cart\OrderStatus`` by ``\Extcode\Cart\Domain\Order\Status``
and ``\Extcode\Cart\OrderHistory`` by ``\Extcode\Cart\Domain\Order\History``:

.. code-block:: php
   :caption: EXT:my_extension/Configuration/OrderStatus.php

   <?php

   use Extcode\Cart\Domain\Order\Status\PaymentStatus;

   return [
       'payment' => [
           'refunded' => [
               'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang_db.xlf:payment.status.refunded',
               'after' => [PaymentStatus::PAID],
           ],
       ],
   ];

.. index:: PHP-API
