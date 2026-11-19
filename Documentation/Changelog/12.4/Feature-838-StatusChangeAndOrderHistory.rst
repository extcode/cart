.. include:: ../../Includes.rst.txt

=====================================================================
Feature: #838 - Protect status changes and keep an order history
=====================================================================

See `Issue 838 <https://github.com/extcode/cart/issues/838>`__

Description
===========

The payment and the shipping status in the order details of the backend
module are changed by the new
``\Extcode\Cart\OrderStatus\StatusChangeService``:

*  Only registered status are accepted.
*  The form sends the status it showed. If the order has another status in
   the meantime, e.g. changed in another tab, nothing is changed and a flash
   message tells the editor.
*  Submitting the current status changes nothing.
*  The status is updated atomically, so of several requests with the same
   expected status only one changes it.

Every change is written to the new table ``tx_cart_domain_model_order_change``
with the old and the new value, the date, the source (backend, frontend, API
or command line), the backend user, if there is one, and an optional
initiator, e.g. a payment provider. The order details of the backend module show these
changes as order history. The table is generic, other changes of an order can
be written to it as well. The command ``order:cleanup`` removes the order
history of the orders it deletes.

See :ref:`developer-order-status-change`.

Impact
======

Run the database analyzer to create the table
``tx_cart_domain_model_order_change``.

The forms of the payment and the shipping status in
``Resources/Private/Partials/Backend/Order/Show/Properties.html`` send the
fields ``status`` and ``fromStatus`` and pass the payment or the shipping as
argument of the action. Overridden partials have to be adapted.

``\Extcode\Cart\Controller\Backend\Order\PaymentController`` and
``\Extcode\Cart\Controller\Backend\Order\ShippingController`` extend the new
``AbstractStatusController`` and get the ``StatusChangeService`` injected
instead of the repositories. The event
``\Extcode\Cart\Event\Order\UpdateServiceEvent`` is only dispatched if the
status was changed.

The backend and the frontend ``OrderController`` get the
``OrderStatusRegistry`` (the backend one also the ``OrderHistoryService``)
injected by the constructor instead of an inject method. Subclasses which
override the constructor have to be adapted.

.. index:: Backend, Database
