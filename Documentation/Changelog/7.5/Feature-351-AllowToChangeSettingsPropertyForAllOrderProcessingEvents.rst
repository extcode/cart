.. include:: ../../Includes.rst.txt

=================================================================================
Feature: 351 - Allow to Change $settings Property for all Order Processing Events
=================================================================================

See `Issue 351 <https://github.com/extcode/cart/issues/351>`__

Description
===========

It is in the EventListeners

* \Extcode\Cart\Event\Order\CreateEvent,
* \Extcode\Cart\Event\Order\FinishEvent,
* \Extcode\Cart\Event\Order\PaymentEvent,
* \Extcode\Cart\Event\Order\StockEvent, and
* \Extcode\Cart\Event\ProcessOrderCreateEvent

possible to use the setSettings() method to change the settings for later EventListeners.

Impact
======

No Impact.
