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
   This is an experimental feature. Classes and the TypoScript configuration
   may change in minor versions, even incompatibly. Enable it only if you
   accept to adapt your configuration and templates after an update.

The feature is disabled by default. Enable the feature toggle e.g. in
:file:`config/system/additional.php`:

.. code-block:: php
   :caption: config/system/additional.php

   $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['cart.experimental.statusChangeMails'] = true;

Without the feature toggle no emails are sent on status changes, the
configuration below is ignored.

Each email is configured for a transition of the status under
``plugin.tx_cart.statusChange.mail.payment`` or
``plugin.tx_cart.statusChange.mail.shipping``:

.. code-block:: typoscript
   :caption: EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart.statusChange.mail {
       payment {
           pendingToPaid {
               from = pending
               to = paid
               recipients {
                   buyer < plugin.tx_cart.mail.buyer
               }
           }
       }

       shipping {
           shipped {
               from = *
               to = shipped
               recipients {
                   buyer < plugin.tx_cart.mail.buyer
                   seller < plugin.tx_cart.mail.seller
                   seller.bccAddress = warehouse@example.com
               }
           }
       }
   }

The copy operator ``<`` copies ``plugin.tx_cart.mail.buyer`` at the time it is
parsed, so it has to be defined before. Single values can be changed for one
configuration afterwards.

The key of a configuration (e.g. ``pendingToPaid``) is the name of its
template folder, see below.

.. confval:: from

   :Type: string

   The old status, several ones separated by commas, or ``*`` for any status.

.. confval:: to

   :Type: string

   The new status.

.. confval:: recipients.buyer

   :Type: array

   Sends the email to the buyer, to the email address of the billing address.
   The keys are the ones of ``plugin.tx_cart.mail.buyer``: ``fromName``,
   ``fromAddress``, ``ccAddress``, ``bccAddress`` and ``replyToAddress``.

.. confval:: recipients.seller

   :Type: array

   Sends the email to the seller. The keys are the ones of
   ``plugin.tx_cart.mail.seller``: ``fromName``, ``fromAddress``,
   ``toAddress``, ``ccAddress`` and ``bccAddress``. The seller replies to the
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
``initiator``) and ``settings``.

Errors
======

An email which can not be sent, e.g. because of a missing template, an
invalid configuration or an invalid email address (in TypoScript or of the
buyer), does not undo the change of the status. The editor gets an error
message in the backend, and the error is written to the order log
(``tx_cart_domain_model_order_log``, type ``statusChangeMail``).

The TypoScript is read from the current request: in the backend module the one
of the storage folder of the order, in the frontend the one of the requested
page. Without TypoScript, e.g. on the command line, no emails are sent and a
warning is written to the order log.
