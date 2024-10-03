.. include:: ../../Includes.rst.txt

===================================================
Feature: #309 - Improve ProcessOrderCheckStockEvent
===================================================

See `Issue 309 <https://github.com/extcode/cart/issues/309>`__

Description
===========

When a customer wants to send an order the
:php:`Classes/Event/ProcessOrderCheckStockEvent.php` is called. This Event
existed already before but with limited functionality. The purpose of this Event
is to allow product extensions (e.g. `EXT:cart_products`) to cancel the order
process if any product in the order is insufficient in stock. This means it's
only working if stock handling is enabled.

With this feature the functionality of :php:`ProcessOrderCheckStockEvent` is
extended:

* EventListener can set a flag that not every product of the order that a
  customer wants to make are available (:php:`setNotEveryProductAvailable()`).
* EventListener can add custom messages to the Event, e.g. for every product
  which is not available.

The :php:`Classes/Controller/Cart/OrderController.php` takes the flag and the
messages into account: If the flag is true (= not every product is available)
it will add the messages as flash messages queue and redirect back to the
cart. This means the order form is rendered again with the flash messages.
An example for an EventListener will be introduced in `EXT:cart_products`.

Impact
======

This change should not have any negative impact. Your product extensions can
now implement an EventListener which listens to the
`ProcessOrderCheckStockEvent` to add messages about products which are
insufficient amount in your stock. An example for such a EventListener will be
added to `EXT:cart_products`.

.. index:: Backend
