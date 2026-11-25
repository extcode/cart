.. include:: ../../Includes.rst.txt

===================================================================
Feature: #905 - Create shipments and derive the shipping status
===================================================================

See `Issue 905 <https://github.com/extcode/cart/issues/905>`__

Description
===========

Shipments (see :doc:`Feature-902-OrderShipments`) are created and changed by
services in ``\Extcode\Cart\Domain\Order\Fulfillment``. There is no backend UI
yet, it follows in a later version.

``ShipmentCreationService::create()`` stores a shipment with its products in
one database transaction. The order is locked while the quantities are
checked, so concurrent requests cannot ship more than ordered:

*  Only shippable products of the order with a count greater than 0 can be
   added, otherwise an ``\InvalidArgumentException`` is thrown.
*  The counts of all shipments which are not canceled must not exceed the
   ordered count, otherwise nothing is stored and the outcome is
   ``ShipmentCreationOutcome::QuantityExceeded``.
*  The creation is written to the order history with the type
   ``shipment_status``, the subject ``shipment:<uid>`` and the products and
   counts as data.

``ShipmentStatusChangeService::change()`` changes the status of a shipment
atomically like the ``StatusChangeService`` does for payment and shipping. A
shipment can only be canceled before it is shipped, a canceled shipment does
not change anymore (``StatusChangeOutcome::NotAllowed``). Listeners of the new
events ``\Extcode\Cart\Event\Order\BeforeShipmentStatusChangeEvent`` (with
veto) and ``\Extcode\Cart\Event\Order\AfterShipmentStatusChangeEvent`` react to
a change.

``OrderProductQuantityCalculator::calculateForOrderItem()`` returns the
``OrderProductQuantities`` of an order: per ordered product the ordered count,
the count in shipments which are not canceled, the shipped count (shipments
with the status ``shipped`` or ``delivered``) and the count which is still
available for a shipment.

After a shipment is created or its status is changed, the shipping status of
the order is derived from the shipments and changed via the
``StatusChangeService``, so it is written to the order history and the events
and status mails work like for a manual change:

*  ``partially_shipped``: some quantities are shipped, some are open.
*  ``shipped``: all ordered quantities of shippable products are shipped.
*  Nothing is shipped (anymore): ``open``, ``on_hold`` and ``in_process`` stay
   as set manually; a derived ``partially_shipped`` or ``shipped`` falls back
   to ``in_process``.

With the feature toggle ``cart.orderFulfillment`` enabled, an order without
shippable products gets the shipping status ``not_required`` when it is
persisted.

Impact
======

Without the feature toggle nothing changes for existing shops.

``\Extcode\Cart\Domain\Order\Status\StatusChangeOutcome`` has the new case
``NotAllowed``. Code that matches all cases of the enum has to handle it.

.. index:: PHP-API
