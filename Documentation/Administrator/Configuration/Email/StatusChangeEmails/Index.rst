.. include:: ../../../../Includes.rst.txt

.. _status-change-emails:

=========================
Emails on status changes
=========================

Besides the emails at the end of an order, EXT:cart can send emails when the
payment or the shipping status of an order is changed, e.g. in the order
details of the backend module or by a payment provider. No such emails are
configured by default.

.. WARNING::
   This is an experimental feature. Classes and the configuration
   may change in minor versions, even incompatibly. Enable it only if you
   accept to adapt your configuration and templates after an update.

The feature is disabled by default. Enable the feature toggle e.g. in
:file:`config/system/additional.php`:

.. code-block:: php
   :caption: config/system/additional.php

   $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['cart.experimental.statusChangeMails'] = true;

Without the feature toggle no emails are sent on status changes, the
configuration below is ignored.

Each email is configured for a transition of the status in the site settings
``extcode.cart.statusChange.mail.payment`` or
``extcode.cart.statusChange.mail.shipping`` of the site of the order, e.g. in
:file:`config/sites/<site>/settings.yaml`:

.. code-block:: yaml
   :caption: config/sites/<site>/settings.yaml

   extcode:
     cart:
       statusChange:
         mail:
           payment:
             pendingToPaid:
               from: pending
               to: paid
               recipients: [buyer]
           shipping:
             shipped:
               from: '*'
               to: shipped
               recipients: [buyer, seller]

The site is the one of the storage folder of the order, so the emails are sent
on the command line too, e.g. by a command or a scheduler task. The sender and
the recipients are the email addresses of the site settings
``extcode.cart.mail.buyer`` and ``extcode.cart.mail.seller`` of the site set
``extcode/cart-mail``, see :ref:`mail_addresses`.

The key of a configuration (e.g. ``pendingToPaid``) is the name of its
template folder, see below.

.. confval:: from

   :Type: string or list

   The old status, a list of status (or several ones separated by commas), or
   ``*`` for any status.

.. confval:: to

   :Type: string

   The new status.

.. confval:: recipients

   :Type: list

   ``buyer`` and/or ``seller`` (or both separated by commas). The email to the
   buyer is sent to the email address of the billing address, the email to the
   seller to ``extcode.cart.mail.seller.toAddress``. The seller replies to the
   buyer.

.. confval:: template

   :Type: string
   :Default: ``Mail/StatusChange/<Payment|Shipping>/<Key>``

   The template folder, the template of a recipient is
   ``<template>/Buyer.html`` or ``<template>/Seller.html``. By default the
   folder is the key of the configuration with an upper case first letter,
   e.g. ``Mail/StatusChange/Payment/PendingToPaid/Buyer.html``. Without
   ``template`` the key may only contain letters and digits and has to start
   with a letter.

Several configurations may match one change, each sends its emails. The
templates are searched in the template root paths of the email API, see
:ref:`overwrite-email-templates`. They get the variables ``orderItem``,
``orderChange`` (with ``oldValue``, ``newValue``, ``source`` and
``initiator``) and ``settings`` (``plugin.tx_cart.settings``, empty without
TypoScript, e.g. on the command line).

The emails get the attachments of the ``\Extcode\Cart\Event\Mail\AttachmentEvent``
like the emails at the end of an order, e.g. the files of
``plugin.tx_cart.mail.buyer.attachments``, see :ref:`mail_attachments`. The
attachments configured in TypoScript are not available on the command line.

Errors
======

An email which can not be sent, e.g. because of a missing template, an
invalid configuration, an invalid email address (in the site settings or of
the buyer) or a failing listener of the ``AttachmentEvent``, does not undo the
change of the status. The editor gets an error message in the backend, and the
error is written to the order log (``tx_cart_domain_model_order_log``, type
``statusChangeMail``).

If the storage folder of the order is in no site, no emails are sent and a
warning is written to the order log.
