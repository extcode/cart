.. include:: ../../Includes.rst.txt

===============================
Deprecation: #845 - MailHandler
===============================

See `Issue 845 <https://github.com/extcode/cart/issues/845>`__

Description
===========

The class ``\Extcode\Cart\Service\MailHandler`` is deprecated. The emails at
the end of an order are sent by ``\Extcode\Cart\Mail\OrderMailSender``, which
uses the same classes as the emails on status changes:

*  ``\Extcode\Cart\Configuration\Loader\MailConfigurationLoaderInterface``
   loads the configuration of the emails to the buyer and to the seller. The
   implementation ``\Extcode\Cart\Configuration\Loader\TypoScript\MailConfigurationLoader``
   reads ``plugin.tx_cart.mail.buyer`` and ``plugin.tx_cart.mail.seller``,
   overridden by the settings of the plugin, e.g.
   ``settings.buyer.emailFromAddress``.
*  ``\Extcode\Cart\Mail\Mailer`` sends the email. It takes the attachments as
   new argument ``$attachments``.

The listener ``\Extcode\Cart\EventListener\Order\Finish\Email`` sends the
emails with the ``OrderMailSender`` if the feature toggle
``cart.useNewOrderMailSender`` is enabled:

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['cart.useNewOrderMailSender'] = true;

Without the toggle the listener uses the ``MailHandler`` as before, so a
``MailHandler`` replaced in the dependency injection keeps working.

Impact
======

Sending an email with the ``MailHandler`` triggers a deprecation. The class
and the feature toggle will be removed in cart 13.0, the emails are then
always sent by the ``OrderMailSender``.

The ``MailHandler`` still works. It sends the emails with the
``OrderMailSender``, using the addresses of its getters and setters.

The configuration and the templates of the emails are not changed. With the
feature toggle:

*  The seller needs ``plugin.tx_cart.mail.seller.toAddress`` (or
   ``settings.seller.emailToAddress`` of the plugin). Without it an error is
   written to the order log of each order. Previously the email to the seller
   was skipped.
*  Invalid email addresses in the configuration are written to the order log
   before the email is sent. Previously sending the email failed.

Affected Installations
======================

All installations with extensions which use or replace the ``MailHandler``,
e.g. to send the emails of an order again, and all installations without
email address of the seller.

Migration
=========

Configure the email address of the seller and enable the feature toggle
``cart.useNewOrderMailSender``.

Extensions which replace the ``MailHandler`` move the changes to an own
``\Extcode\Cart\Configuration\Loader\MailConfigurationLoaderInterface``
implementation or to a listener of the ``FinishEvent``. Extensions which use
the ``MailHandler`` use the ``OrderMailSender``:

.. code-block:: php

   use Extcode\Cart\Mail\OrderMailSender;

   public function __construct(
       private readonly OrderMailSender $orderMailSender,
   ) {}

   // before: $mailHandler->setCart($cart); $mailHandler->sendBuyerMail($orderItem);
   $this->orderMailSender->sendBuyerMail($orderItem, $cart);
   $this->orderMailSender->sendSellerMail($orderItem, $cart);

To send an email with an own configuration, e.g. other addresses, create a
``\Extcode\Cart\Mail\BuyerMailConfiguration`` or
``\Extcode\Cart\Mail\SellerMailConfiguration`` and send it with
``OrderMailSender::send()``.

.. index:: Frontend, PHP-API
