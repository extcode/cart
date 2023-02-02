.. include:: ../../Includes.txt

Events
======

The extcode/cart extension already uses events in some places, especially to integrate custom
requirements in the ordering process. You can register your own EventListener for the following
events:

.. container:: table-row

   Event Class
      `\Extcode\Cart\Event\CheckProductAvailabilityEvent`
   Description
      This event is triggered when it is necessary to check whether a product
      is still available in sufficient quantity in the warehouse. The product
      extensions should implement an EventListener that checks the products
      of this extension. If a product is no longer available or not available
      in sufficient quantity, the property $available must be set to false.
      In addition, the EventListener can pass messages.
      If there is no availability check in the product extension, no
      EventListener needs to be implemented.

.. container:: table-row

   Event Class
      `\Extcode\Cart\Event\ProcessOrderCheckStockEvent`
   Description
      The event is triggered in the `\Extcode\Cart\Controller\Cart\OrderController::createAction`
      and allows to cancel the further ordering process if the number of
      products in the shopping cart is no longer available in the meantime.

.. container:: table-row

   Event Class
      `\Extcode\Cart\Event\RetrieveProductsFromRequestEvent`
   Description
      This event is triggered when an "addToCart" form is submitted. The
      extension passes the form data in this event. Each product extension must
      provide an EventListener for its own product type that returns one or
      more products based on this form data. It is up to the product extension
      how to create these products as instances of the class
      `\Extcode\Cart\Domain\Model\Cart\Product`. Products can be loaded from
      the database, but also created based on the form data, or fetched from
      another system via an API.

.. container:: table-row

   Event Class
      `\Extcode\Cart\Event\Order\CreateEvent`
   Description
      This event is used by the extension itself, but can be extended by
      custom EventListeners.
      It is the first event that is called when the order is submitted.
      This event implements the StoppableEventInterface.

.. container:: table-row

   Event Class
      `\Extcode\Cart\Event\Order\NumberGeneratorEvent`
   Description
      It is the second event that is called when the order is submitted.
      This event is used by the extension itself, but can be extended by
      custom EventListeners. The EventListeners that the cart extension
      itself registers to this event can very easily be replaced with
      custom EventListeners.
      The event is also triggered in the backend if an invoice or delivery
      number is subsequently generated there, because they should not be
      generated automatically during the ordering process.
      A Payment provider extensions can also trigger this event if the
      payment process was successful and an invoice is to be sent instead
      of an order confirmation.

.. container:: table-row

   Event Class
      `\Extcode\Cart\Event\Order\PaymentEvent`
   Description
      This third event in the row can be implemented by payment provider
      extensions to forward to the payment provider at this point. In case
      of forwarding, the processing of the following events should be
      prevented via the `StoppableEventInterface`, because
      `\Extcode\Cart\Event\Order\StockEvent` and
      `\Extcode\Cart\Event\Order\FinishEvent` are only necessary if the
      payment process is successful.
      If the payment process is successful, the payment provider extension
      should trigger the events `\Extcode\Cart\Event\Order\StockEvent` and
      `\Extcode\Cart\Event\Order\FinishEvent` itself.


.. container:: table-row

   Event Class
      `\Extcode\Cart\Event\Order\PersistOrderEvent`
   Description
      TODO

.. container:: table-row

   Event Class
      `\Extcode\Cart\Event\Order\StockEvent`
   Description
      The fourth event should again be used by product extensions if they
      provide stock management. The EventListener should perform stock
      management for products of this extension. Either count down the stock
      counter in the database or tell the product service via an interface
      which products were bought how often.

.. container:: table-row

   Event Class
      `\Extcode\Cart\Event\Order\FinishEvent`
   Description
      The fifth and final event in the ordering process is to finish the order.
      This is mainly used by the cart extension itself to finalize the order
      process. Here, among other things, e-mails are sent. Other extensions can
      use this event to generate PDF documents, register users or assign
      registered users to another user group.
