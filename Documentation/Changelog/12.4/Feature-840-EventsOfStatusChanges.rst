.. include:: ../../Includes.rst.txt

=================================================================
Feature: #840 - Events before and after a status change of orders
=================================================================

See `Issue 840 <https://github.com/extcode/cart/issues/840>`__

Description
===========

The ``StatusChangeService`` (#838) dispatches two new events when it changes
the payment or the shipping status of an order:

``\Extcode\Cart\Event\Order\BeforeStatusChangeEvent``
   Dispatched before the change with the order, the payment or the shipping,
   the old and the new status, the source, the initiator and the backend user.
   Listeners can prevent the change with ``veto('reason')``, the reason is
   shown as flash message in the order details of the backend module.

``\Extcode\Cart\Event\Order\AfterStatusChangeEvent``
   Dispatched after the status was changed and written to the order history,
   e.g. to send an email or to create a shipping label. The uid of the stored
   change identifies the change, so a listener can execute an action only
   once.

``StatusChangeService::change()`` returns the result object
``\Extcode\Cart\OrderStatus\StatusChangeResult`` with the outcome (the enum
``StatusChangeOutcome``, with the new case ``Vetoed``), the reason of a veto
and the stored change.

See :ref:`developer-order-status-events`.

Impact
======

Extensions can react on status changes of orders and prevent them.

.. index:: Backend, PHP-API
