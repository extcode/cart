.. include:: ../../Includes.rst.txt

==========================================================================
Feature: #866 - Status methods of the payment and the shipping of an order
==========================================================================

See `Issue 866 <https://github.com/extcode/cart/issues/866>`__

Description
===========

The payment (``\Extcode\Cart\Domain\Model\Order\Payment``) and the shipping
(``\Extcode\Cart\Domain\Model\Order\Shipping``) of an order have new methods
for the status of EXT:cart:

=========  ==================================================================
Service    Methods
=========  ==================================================================
payment    ``isOpen()``, ``isPending()``, ``isPaid()``, ``isCanceled()``
shipping   ``isOpen()``, ``isOnHold()``, ``isInProcess()``, ``isShipped()``
=========  ==================================================================

Further status added by other extensions have no method, compare them with
``getStatus()``.

Impact
======

Extensions, e.g. for payment providers, use the methods instead of comparing
the status with strings themselves.

.. index:: PHP-API
