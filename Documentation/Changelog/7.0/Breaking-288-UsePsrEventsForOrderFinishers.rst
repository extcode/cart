.. include:: ../../Includes.txt

======================================================
Breaking: #288 - Use PSR-14 Events for Order Finishers
======================================================

See :issue:`288`

Description
===========

Instead of defining the finishers for processing orders via TypoScript, the `ProcessOrderCreateEvent` event is now
triggered at this point.
The previous finishers are registered accordingly to this event via Service.yaml.
This offers the advantage that you can define with `before` and `after`, which dependencies must be fulfilled for
the order, without presetting them completely.

Two finishers were also removed in the process. One is the StockFinisher, which each product extension must register
for itself.

On the other hand, the PaymentFinisher was also removed. Payment provider extensions must now trigger their own event
and register all required finishers for this event themselves. This gives more flexibility to the payment provider
extensions. For example, when to call the finishers for stock management.

Affected Installations
======================

For installations that do not have their own finishers registered, no adjustments should be required.
Installations that have called their own finishers in the ordering process must be registered to the new event.

Migration
=========

The finisher class must bring an __invoke() method and gets passed an appropriate event object.

Furthermore, the finisher must be configured in the Service.yaml to the event `ProcessOrderCreateEvent`.
The TypoScript configuration can be removed.

.. index:: Backend
