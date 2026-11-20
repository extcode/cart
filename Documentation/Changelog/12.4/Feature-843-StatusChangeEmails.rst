.. include:: ../../Includes.rst.txt

===================================================
Feature: #843 - Emails on status changes of orders
===================================================

See `Issue 843 <https://github.com/extcode/cart/issues/843>`__

Description
===========

EXT:cart sends emails when the payment or the shipping status of an order is
changed, for the transitions configured in TypoScript under
``plugin.tx_cart.statusChange.mail.payment`` and
``plugin.tx_cart.statusChange.mail.shipping``:

.. code-block:: typoscript

   plugin.tx_cart.statusChange.mail.payment {
       pendingToPaid {
           from = pending
           to = paid
           recipients.buyer < plugin.tx_cart.mail.buyer
       }
   }

The recipients ``buyer`` and ``seller`` take the keys of
``plugin.tx_cart.mail.buyer`` and ``plugin.tx_cart.mail.seller``, so they can
be copied. The template of the example is
``Mail/StatusChange/Payment/PendingToPaid/Buyer.html``.

The listener ``\Extcode\Cart\EventListener\Order\StatusChange\Mail`` of the
``AfterStatusChangeEvent`` sends the emails. Listeners of this event can add
errors (``addError()``), which are returned in ``StatusChangeResult::$errors``
and shown to the editor in the backend, e.g. if an email can not be sent.

See :ref:`status-change-emails`.

Impact
======

The feature is experimental and disabled by default. It is enabled with the
feature toggle ``cart.experimental.statusChangeMails``:

.. code-block:: php

   $GLOBALS['TYPO3_CONF_VARS']['SYS']['features']['cart.experimental.statusChangeMails'] = true;

Classes and the TypoScript configuration of the feature may change in minor
versions, even incompatibly.

No emails are configured by default, the emails at the end of an order are
not changed.

.. index:: Backend, TypoScript
