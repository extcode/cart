.. include:: ../../Includes.rst.txt

=====================================
Feature: #902 - Shipments with lines
=====================================

See `Issue 902 <https://github.com/extcode/cart/issues/902>`__

Description
===========

An order can store several shipments (partial deliveries). This change adds
the data model only; creating shipments, deriving the shipping status of the
order and the backend UI follow in later versions.

*  ``tx_cart_domain_model_order_shipment``: order item, status, carrier,
   tracking number, tracking URL, shipped date, note and whether the shipment
   is a replacement for returned products.
*  ``tx_cart_domain_model_order_shipment_product``: the products of a shipment,
   the shipped count of an ordered product.

Shipments are no Extbase models. They are only written by services in
database transactions, so integrators cannot change them by accident. The
readonly objects ``\Extcode\Cart\Domain\Order\Fulfillment\Shipment`` and
``\Extcode\Cart\Domain\Order\Fulfillment\ShipmentProduct`` are read and stored by
``\Extcode\Cart\Domain\Order\Fulfillment\ShipmentRepository``. The TCA of both
tables is read only, the shipments are shown in the order item record.

New status (see :ref:`developer-order-status`):

*  The new type ``shipment`` with the status ``open``, ``in_process``,
   ``shipped``, ``delivered`` and ``canceled``
   (``\Extcode\Cart\Domain\Order\Fulfillment\ShipmentStatus``). Extensions
   register further shipment status in :file:`Configuration/OrderStatus.php`.
*  The shipping status ``partially_shipped`` and ``not_required`` (no ordered
   product is shippable).

The new feature toggle ``cart.orderFulfillment`` is disabled by default. It
sets the shipping status ``not_required`` for orders without shippable
products, see :doc:`Feature-905-CreateShipmentsAndDeriveShippingStatus`, and
will enable the backend UI for shipments.

Impact
======

The database schema has to be updated. Nothing changes for existing orders.

The select of the shipping status in the backend module offers the new status
``partially_shipped`` and ``not_required``.

``\Extcode\Cart\Domain\Order\Status\ServiceType`` has the new case
``Shipment``. Code that matches all cases of the enum has to handle it.

.. index:: Backend, Database, PHP-API
