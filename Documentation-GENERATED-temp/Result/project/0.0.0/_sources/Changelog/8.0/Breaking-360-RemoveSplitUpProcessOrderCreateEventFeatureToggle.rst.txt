.. include:: ../../Includes.txt

=======================================================================
Breaking: #360 - Remove Split Up ProcessOrderCreateEvent Feature Toggle
=======================================================================

See :issue:`360`

Description
===========

The configuration 'SplitUpProcessOrderCreateEvent' was removed. As a
result, several events are now triggered one after the other when the
order is placed in the frontend.

The current sequence of these events is:

* `\Extcode\Cart\Event\Order\CreateEvent`
* `\Extcode\Cart\Event\Order\NumberGeneratorEvent`
* `\Extcode\Cart\Event\Order\FinishEvent`
* `\Extcode\Cart\Event\Order\PaymentEvent`
* `\Extcode\Cart\Event\Order\StockEvent`

Thereby the `\Extcode\Cart\Event\Order\NumberGeneratorEvent` was added in
this version and contains in this version the registered event
`cart--order--create--order-number` which was previously contained in
`\Extcode\Cart\Event\Order\CreateEvent` .

Affected Installations
======================

This affects all installations that have not yet been switched via the
FeatureToggle and have registered their own EventListeners on
`Extcode\Cart\Event\ProcessOrderCreateEvent`.
Furthermore, installations that have registered their own EventListeners on
`\Extcode\Cart\Event\Order\CreateEvent` are affected.

Migration
=========

In case the FeatureToggle was not activated, but no own EventListeners were
registered, nothing has to be adjusted at all. The ordering process should
be tested.

In case the FeatureToggle was not activated and own EventListeners were
registered on `Extcode\Cart\Event\ProcessOrderCreateEvent`, the registered
EventListeners are to be registered on the new events. The order process
should be checked in any case.

In case the FeatureToggle was enabled and own EventListeners were registered
to `\Extcode\Cart\Event\Order\CreateEvent`, check if these EventListeners must
not be registered to `\Extcode\Cart\Event\Order\NumberGeneratorEvent`.

.. index:: Template, Frontend, Backend
