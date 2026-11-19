.. include:: ../../Includes.rst.txt

============================================================
Feature: #836 - Register own payment and shipping status
============================================================

See `Issue 836 <https://github.com/extcode/cart/issues/836>`__

Description
===========

The payment and shipping status of the orders are no longer fixed strings in
the TCA and the backend controller. They are registered in
:file:`Configuration/OrderStatus.php` with a label and their order
(``before`` and ``after``). Other extensions add their own status or change
the ones of EXT:cart in their own :file:`Configuration/OrderStatus.php`.

The registered status are used for the filter of the order list, the labels
in the order list, the selects in the order details of the backend module,
the order details in the frontend and the items of the status fields in the
TCA. The new ViewHelper ``cart:order.statusLabel`` renders the label of a
status. The identifiers of the status of EXT:cart are available as constants
of ``\Extcode\Cart\Domain\Model\Order\Status\PaymentStatus`` and
``\Extcode\Cart\Domain\Model\Order\Status\ShippingStatus``.

See :ref:`developer-order-status`.

Impact
======

The status fields of ``tx_cart_domain_model_order_payment`` and
``tx_cart_domain_model_order_shipping`` get their items from the
``itemsProcFunc`` ``\Extcode\Cart\Hooks\OrderStatusItemsProcFunc``, the
``items`` in the TCA are empty. Status added to these ``items``, e.g. by a
TCA override, still work but are deprecated, see
:doc:`Deprecation-836-OrderStatusInTcaItems`.

The templates use ``cart:order.statusLabel`` instead of the translation keys
``tx_cart_domain_model_order_payment.status.<status>`` and
``tx_cart_domain_model_order_shipping.status.<status>``. These keys still
exist, so overridden templates keep working for the status of EXT:cart.

The order details in the backend keep a status of an order which is not
registered as option of the select, so saving the form does not change it.

The constructors of ``\Extcode\Cart\Domain\Model\Order\Payment`` and
``\Extcode\Cart\Domain\Model\Order\Shipping`` set the initial status
``open``. The default status of
``\Extcode\Cart\Domain\Model\Order\AbstractService`` is an empty string now.

.. index:: Backend, TCA
