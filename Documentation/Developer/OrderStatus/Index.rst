.. include:: ../../Includes.rst.txt

.. _developer-order-status:

===========================
Payment and shipping status
===========================

Every order has a payment status and a shipping status. EXT:cart registers
these status:

=========  =================================================
Service    Status in their order
=========  =================================================
payment    ``open``, ``pending``, ``paid``, ``canceled``
shipping   ``open``, ``on_hold``, ``in_process``, ``shipped``
=========  =================================================

The identifiers of these status are available as constants of
``\Extcode\Cart\OrderStatus\PaymentStatus`` and
``\Extcode\Cart\OrderStatus\ShippingStatus``, e.g.
``PaymentStatus::PAID``. The constants only name the status of EXT:cart.

The status are no enum, so your own extension can register further status,
e.g. ``refunded`` for the payment or ``delivered`` for the shipping. The
registered status are used in the backend module (the filter of the order
list, the label in the order list and the select in the order details), in
the order details in the frontend and in the TCA of the payment and the
shipping.

Register own status
===================

Add a file :file:`Configuration/OrderStatus.php` to your extension. It
returns the status by service type (``payment`` or ``shipping``) and
identifier. The identifier is stored in the database, it consists of lower
case letters, digits and underscores and starts with a letter.

..  code-block:: php
    :caption: EXT:my_extension/Configuration/OrderStatus.php

    <?php

    use Extcode\Cart\OrderStatus\PaymentStatus;
    use Extcode\Cart\OrderStatus\ShippingStatus;

    return [
        'payment' => [
            'refunded' => [
                'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:payment.status.refunded',
                'after' => [PaymentStatus::PAID],
                'before' => [PaymentStatus::CANCELED],
            ],
        ],
        'shipping' => [
            'delivered' => [
                'label' => 'LLL:EXT:my_extension/Resources/Private/Language/locallang.xlf:shipping.status.delivered',
                'after' => [ShippingStatus::SHIPPED],
            ],
        ],
    ];

A status has these keys:

``label``
    The label of the status, a ``LLL:`` reference or a plain text. Required.

``before``
    A list of status identifiers this status is ordered before.

``after``
    A list of status identifiers this status is ordered after.

The status are ordered by ``before`` and ``after`` like the middlewares in
:file:`Configuration/RequestMiddlewares.php`. Give both to place a status
between two others; with only ``after`` the position relative to the other
following status is not defined. A status may refer to the status of other
extensions, a reference to a status which is not registered is ignored.
Unknown keys, service types and invalid values throw an exception.

The status of EXT:cart are loaded first, the ones of the other extensions in
the order of the extensions. An extension may change a status of EXT:cart or
of an extension loaded before by using the same identifier: the given keys
replace the ones registered before, e.g. ``'pending' => ['label' => '...']``
only changes the label of ``pending``.

The registered status are cached in the core cache, flush the caches after
changing a :file:`Configuration/OrderStatus.php`.

Use the registered status
=========================

The service ``\Extcode\Cart\OrderStatus\OrderStatusRegistry``
returns the registered status in their order:

..  code-block:: php

    use Extcode\Cart\OrderStatus\OrderStatusRegistry;
    use Extcode\Cart\OrderStatus\ServiceType;

    final readonly class MyService
    {
        public function __construct(
            private OrderStatusRegistry $orderStatusRegistry,
        ) {}

        public function isKnownPaymentStatus(string $status): bool
        {
            return $this->orderStatusRegistry->hasStatus(ServiceType::Payment, $status);
        }
    }

``getStatuses()`` returns the ``\Extcode\Cart\OrderStatus\Status``
objects, ``getLocalizedLabels()`` the translated labels by identifier, e.g. for
the options of a select.

In Fluid templates the ViewHelper ``cart:order.statusLabel`` renders the
label of a status. A status which is not registered (anymore) is rendered as
its identifier:

..  code-block:: html

    <cart:order.statusLabel serviceType="payment" status="{orderItem.payment.status}"/>

.. _developer-order-status-change:

Change the status
=================

Change the status of an order with
``\Extcode\Cart\OrderStatus\StatusChangeService`` instead of setting it and
persisting the payment or the shipping, e.g. in the notification of a payment
provider:

..  code-block:: php

    use Extcode\Cart\OrderHistory\OrderChangeSource;
    use Extcode\Cart\OrderStatus\PaymentStatus;
    use Extcode\Cart\OrderStatus\StatusChangeResult;
    use Extcode\Cart\OrderStatus\StatusChangeService;

    // the notification of the payment provider, sent by its server
    $result = $this->statusChangeService->change(
        $orderItem->getPayment(),
        PaymentStatus::PAID,
        $orderItem->getPayment()->getStatus(),
        initiator: 'my_payment_provider',
        source: OrderChangeSource::Api
    );

The optional ``initiator`` tells who or what triggered the change if it is not
a backend user, e.g. your extension, an ERP synchronisation or a scheduler
task.

The source is the channel of the change, the enum
``\Extcode\Cart\OrderHistory\OrderChangeSource``:

``Backend``
    An editor in the backend.

``Frontend``
    The browser of a customer, e.g. redirected back from a payment provider.

``Api``
    A server-to-server request, e.g. the notification (IPN, webhook) of a
    payment provider or a request of an ERP.

``Cli``
    A command or a scheduler task.

Without a given source it is determined from the request (``Backend``,
``Frontend`` or ``Cli``), by default from ``$GLOBALS['TYPO3_REQUEST']``.
TYPO3 handles a server-to-server request as frontend request, so pass
``OrderChangeSource::Api`` for notifications and webhooks. Then the history
tells whether the customer or the server of the payment provider changed the
status.

The service

*  only sets registered status,
*  only changes the status if the order still has the expected status (the
   third argument), so an outdated form or a repeated notification does not
   overwrite a newer status,
*  updates the status atomically, so of several requests with the same
   expected status only one changes it,
*  writes the change to the order history.

It returns a ``\Extcode\Cart\OrderStatus\StatusChangeResult`` with the
``outcome``: ``Changed``, ``Unchanged`` (the order has the status already),
``Stale`` (the order has not the expected status anymore), ``UnknownStatus``
or ``Vetoed`` (a listener prevented the change, see ``vetoReason``). After a
change ``orderChange`` contains the stored change.

.. _developer-order-status-events:

Events
------

``\Extcode\Cart\Event\Order\BeforeStatusChangeEvent``
    Dispatched before the status is changed, with the order, the payment or
    the shipping, the old and the new status, the source, the initiator and
    the backend user. A listener can prevent the change with
    ``veto('reason')``; the reason is shown to the editor in the backend. The
    first veto stops the other listeners.

    Use it for checks only. The change can still fail after this event, e.g.
    if another request changed the status in the meantime.

``\Extcode\Cart\Event\Order\AfterStatusChangeEvent``
    Dispatched after the status was changed and written to the order history,
    the place for side effects like emails or shipping labels. The uid of the
    stored change (``getOrderChange()->uid``) identifies the change; use it to
    execute an action only once.

    A listener which fails adds a message with ``addError()``. The status
    stays changed; the errors are returned in ``StatusChangeResult::$errors``
    and shown to the editor in the backend.

    EXT:cart registers the listener
    ``\Extcode\Cart\EventListener\Order\StatusChange\Mail`` with the identifier
    ``cart--order--status-change--mail``, which sends the emails configured in
    TypoScript, see :ref:`status-change-emails`. It is experimental and only
    active with the feature toggle ``cart.experimental.statusChangeMails``.

Both events are only dispatched by the ``StatusChangeService``, not for an
unknown or unchanged status.

..  code-block:: php
    :caption: EXT:my_extension/Classes/EventListener/RequireDeliveryNumber.php

    use Extcode\Cart\Event\Order\BeforeStatusChangeEvent;
    use Extcode\Cart\OrderStatus\ServiceType;
    use Extcode\Cart\OrderStatus\ShippingStatus;
    use TYPO3\CMS\Core\Attribute\AsEventListener;

    #[AsEventListener('my-extension--order--require-delivery-number')]
    final readonly class RequireDeliveryNumber
    {
        public function __invoke(BeforeStatusChangeEvent $event): void
        {
            if ($event->getServiceType() === ServiceType::Shipping
                && $event->getToStatus() === ShippingStatus::SHIPPED
                && (string) $event->getOrderItem()->getDeliveryNumber() === ''
            ) {
                $event->veto('Generate the delivery number first.');
            }
        }
    }

..  code-block:: php
    :caption: EXT:my_extension/Classes/EventListener/SendPaidEmail.php

    use Extcode\Cart\Event\Order\AfterStatusChangeEvent;
    use Extcode\Cart\OrderStatus\PaymentStatus;
    use Extcode\Cart\OrderStatus\ServiceType;
    use TYPO3\CMS\Core\Attribute\AsEventListener;

    #[AsEventListener('my-extension--order--send-paid-email')]
    final readonly class SendPaidEmail
    {
        public function __invoke(AfterStatusChangeEvent $event): void
        {
            if ($event->getServiceType() !== ServiceType::Payment
                || $event->getToStatus() !== PaymentStatus::PAID
            ) {
                return;
            }

            // send the email, e.g. remember $event->getOrderChange()->uid
            // to send it only once
        }
    }

Order history
-------------

Every change is written to the table ``tx_cart_domain_model_order_change`` and
shown as order history in the order details of the backend module. A change
has a type (e.g. ``OrderChangeType::PAYMENT_STATUS``), the old and the new
value, the date, the source, the backend user, if there is one, and the
initiator. The history shows the backend user, otherwise the initiator with
the source, e.g. "my_payment_provider (API)", otherwise the source.

The table is not limited to status changes. Your extension can write its own
changes of an order with
``\Extcode\Cart\OrderHistory\OrderChangeRepository::add()``, using
an own type, a subject (e.g. ``product:12``) and details in ``data``.

.. WARNING::
   The order history is new and its structure may still change in minor
   versions. If a change cannot be migrated, the existing entries of
   ``tx_cart_domain_model_order_change`` may have to be removed. Do not rely
   on the order history as the only record of status changes yet.

Emails
======

The emails to the buyer and the seller use a template per payment status. If
an order can have your own payment status when it is finished, add the email
templates for it, see :ref:`overwrite-email-templates-status`.
