.. include:: ../../Includes.txt

===================================================
Deprecation: XXX - Split Up ProcessOrderCreateEvent
===================================================

See :issue:`XXX`

Description
===========

In order to better integrate the EventListener in connection with
payment providers, the `\Extcode\Cart\Event\ProcessOrderCreateEvent`
was split into several events.
These implement a new EventInterface and also the
`\Psr\EventDispatcher\StoppableEventInterface`.

`\Extcode\Cart\Event\ProcessOrderCreateEvent` has been marked as
deprecated and will be removed in version 8.x.

Impact
======

Since the event is no longer available, all EventListeners listening
to this event are no longer executed.

Affected Installations
======================

All installations that have their own EventListener registered to the
`\Extcode\Cart\Event\ProcessOrderCreateEvent`.

Migration
=========

The EventListener must implement the `\Extcode\Cart\Event\Order\EventInterface`
and the `\Psr\EventDispatcher\StoppableEventInterface`.

Furthermore, the EventListener must be registered against one of the
new four events and, if necessary, the correct order must be established
for existing EventListeners on the event.
