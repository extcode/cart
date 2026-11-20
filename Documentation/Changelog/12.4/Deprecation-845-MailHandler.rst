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
   implementation ``\Extcode\Cart\Configuration\Loader\SiteSets\MailConfigurationLoader``
   reads the settings of the new site set ``extcode/cart-mail`` from the site
   of the storage page of the order, see below. It works without request too,
   e.g. in a command.
*  ``\Extcode\Cart\Mail\Mailer`` sends the email. It takes the attachments as
   new argument ``$attachments``.

The listener ``\Extcode\Cart\EventListener\Order\Finish\Email`` sends the
emails with the ``OrderMailSender`` if the feature toggle
``cart.useNewOrderMailSender`` is enabled:

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['cart.useNewOrderMailSender'] = true;

Without the toggle the listener uses the ``MailHandler`` as before, so a
``MailHandler`` replaced in the dependency injection keeps working.

The site set ``extcode/cart-mail`` defines typed settings for the addresses,
which can be edited in the backend module "Site Settings":

.. code-block:: yaml
   :caption: config/sites/<site>/settings.yaml

   extcode:
     cart:
       mail:
         buyer:
           fromName: 'Shop'
           fromAddress: 'shop@example.com'
           ccAddress: []
           bccAddress: ['archive@example.com']
           replyToAddress: []
         seller:
           fromName: 'Shop'
           fromAddress: 'shop@example.com'
           toAddress: ['orders@example.com']
           ccAddress: []
           bccAddress: []

The addresses are lists, not comma separated strings. ``plugin.tx_cart.mail``
and the email settings of the cart plugin (FlexForm) are not used by the
``OrderMailSender``. Attachments are still configured in
``plugin.tx_cart.mail.<buyer|seller>.attachments`` and ``attachDocuments``.

Impact
======

Sending an email with the ``MailHandler`` triggers a deprecation. The class
and the feature toggle will be removed in cart 13.0, the emails are then
always sent by the ``OrderMailSender``.

The ``MailHandler`` still works. It sends the emails with the
``OrderMailSender``, using the addresses of its getters and setters.

The templates of the emails are not changed. With the feature toggle:

*  The addresses are read from the site settings of the set
   ``extcode/cart-mail`` instead of TypoScript and the FlexForm of the plugin.
*  The seller needs ``extcode.cart.mail.seller.toAddress``. Without it an
   error is written to the order log of each order. Previously the email to
   the seller was skipped.
*  If the storage page of the order is in no site, an error is written to the
   order log.
*  Invalid email addresses in the configuration are written to the order log
   before the email is sent. Previously sending the email failed.
*  Errors of the template, e.g. a view helper which needs a request, and of
   listeners of the ``AttachmentEvent`` are written to the order log with the
   reason, the email is not sent. Previously a failing listener of the
   ``AttachmentEvent`` stopped the order.

Affected Installations
======================

All installations which want to use the ``OrderMailSender`` before cart 13.0,
and all installations with extensions which use or replace the ``MailHandler``,
e.g. to send the emails of an order again.

Migration
=========

Add the site set ``extcode/cart-mail`` to the dependencies of the site, move
the addresses from ``plugin.tx_cart.mail`` and the cart plugin to the site
settings ``extcode.cart.mail.*`` and enable the feature toggle
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

.. index:: Frontend, PHP-API, TypoScript
