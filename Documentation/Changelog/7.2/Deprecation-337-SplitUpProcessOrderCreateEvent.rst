.. include:: ../../Includes.txt

===============================================
Feature: 337 - Split Up ProcessOrderCreateEvent
===============================================

See :issue:`337`

Description
===========

In order to better integrate the EventListener in connection with
payment providers, the `\Extcode\Cart\Event\ProcessOrderCreateEvent`
was split into several events.
These implement a new EventInterface and also the
`\Psr\EventDispatcher\StoppableEventInterface`.

.. NOTE::
   `\Extcode\Cart\Event\ProcessOrderCreateEvent` has been marked as
   deprecated and will be removed in version 8.x.

The new events in the order of their dispatch:

- `\Extcode\Cart\Event\Order\CreateEvent`
- `\Extcode\Cart\Event\Order\StockEvent`
- `\Extcode\Cart\Event\Order\PaymentEvent`
- `\Extcode\Cart\Event\Order\FinishEvent`

The new events can be activated via a feature toggle in :file:`LocalConfiguration.php`
or :file:`AdditionalConfiguration.php`::

      $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['SplitUpProcessOrderCreateEvent'] = true;

The cart extension itself currently registers `\Extcode\Cart\EventListener\ProcessOrderCreate\Order`
and `\Extcode\Cart\EventListener\ProcessOrderCreate\Order` on `\Extcode\Cart\Event\Order\CreateEvent`.
And the extension registers `\Extcode\Cart\EventListener\ProcessOrderCreate\Email`
and `\Extcode\Cart\EventListener\ProcessOrderCreate\ClearCart` on `\Extcode\Cart\Event\Order\FinishEvent`.

The product extensions extcode/cart-products, extcode/cart-events, and extcode/cart-books will
register the EventListeners to `\Extcode\Cart\Event\Order\StockEvent`.

The payment methods provider extensions should be registered to the `\Extcode\Cart\Event\Order\PaymentEvent`.
These should return true for the `isPropagationStopped()` if the payment process cannot be completed
immediately, to prevent sending emails and clearing the cart session directly. The emails and clearing cart
is then the responsibility of the extension. Of course, the extension itself can send appropriate events and
the EventListener from this extension can be registered to it.

Impact
======

If the feature toggle has been activated, the event
`\Extcode\Cart\Event\ProcessOrderCreateEvent` will no longer be triggered.
All EventListeners registered to this event must be registered to one of the new events.

.. index:: Backend, Frontend
