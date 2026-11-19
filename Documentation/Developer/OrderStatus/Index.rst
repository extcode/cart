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
``\Extcode\Cart\Domain\Model\Order\Status\PaymentStatus`` and
``\Extcode\Cart\Domain\Model\Order\Status\ShippingStatus``, e.g.
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

    use Extcode\Cart\Domain\Model\Order\Status\PaymentStatus;
    use Extcode\Cart\Domain\Model\Order\Status\ShippingStatus;

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

The service ``\Extcode\Cart\Configuration\OrderStatus\OrderStatusRegistry``
returns the registered status in their order:

..  code-block:: php

    use Extcode\Cart\Configuration\OrderStatus\OrderStatusRegistry;
    use Extcode\Cart\Domain\Model\Order\Status\ServiceType;

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

``getStatuses()`` returns the ``\Extcode\Cart\Domain\Model\Order\Status\Status``
objects, ``getLocalizedLabels()`` the translated labels by identifier, e.g. for
the options of a select.

In Fluid templates the ViewHelper ``cart:order.statusLabel`` renders the
label of a status. A status which is not registered (anymore) is rendered as
its identifier:

..  code-block:: html

    <cart:order.statusLabel serviceType="payment" status="{orderItem.payment.status}"/>

Emails
======

The emails to the buyer and the seller use a template per payment status. If
an order can have your own payment status when it is finished, add the email
templates for it, see :ref:`overwrite-email-templates-status`.
