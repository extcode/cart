.. include:: ../../../../Includes.txt
.. _mail_addresses:
===============
Email addresses
===============

Email addresses must be configured for sending emails. This can be done in
the backend in the plugin settings, but also configured via TypoScript.

.. code-block:: typoscript
   :caption: Can be set in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       mail {
           // Used for emails sent to the customer (=buyer)
           buyer {
               fromName = Your Brand name
               fromAddress = cart.buyer.sender@example.com
               ccAddress = cart.buyer.cc1@example.com, cart.buyer.cc2@example.com
               bccAddress = cart.buyer.bcc1@example.com, cart.buyer.bcc2@example.com
               replyToAddress = cart.buyer.reply@example.com
           }

           // Used for emails sent to the shop owner (=seller)
           seller {
               fromName = Cart TYPO3 System
               fromAddress = cart.seller.sender@example.com
               toAddress = cart.seller.to1@example.com, cart.seller.to2@example.com
               ccAddress = cart.seller.cc1@example.com, cart.seller.cc2@example.com
               bccAddress = cart.seller.bcc1@example.com, cart.seller.bcc2@example.com
           }
       }
   }

plugin.tx_cart.mail
===================

.. confval:: buyer.fromName

   :Type: string

   Name displayed for the email address sent to the buyer (=customer).

.. confval:: buyer.fromAddress

   :Type: string

   Defines from which sender address the emails are sent to the buyer.

.. confval:: buyer.ccAddress

   :Type: string

   Defines to which addresses the email should be sent in CC (Carbon Copy).

   Multiple recipients can be given separated by commas.

.. confval:: buyer.bccAddress

   :Type: string

   Defines to which addresses the email should be sent in BCC
   (Blind Carbon Copy).

   Multiple recipients can be given separated by commas.

.. confval:: buyer.replyToAddress

   :Type: string

   Defines to which address should be used as Reply To.
   This will override the `['MAIL']['defaultMailReplyToAddress']` configuration.


.. confval:: seller.fromName

   :Type: string

   Name displayed for the email address sent to the seller (=shop owner).

.. confval:: seller.fromAddress

   :Type: string

   Defines from which sender address the emails are sent to the seller/shop
   operator.

.. confval:: seller.toAddress

   :Type: string

   Defines to which recipient addresses the emails to the seller/shop operator
   are sent.

   Multiple recipients can be given separated by commas.

.. confval:: seller.ccAddress

   :Type: string

   Defines to which addresses the email should be sent in CC (Carbon Copy).

   Multiple recipients can be given separated by commas.

.. confval:: seller.bccAddress

   :Type: string

   Defines to which addresses the email should be sent in BCC
   (Blind Carbon Copy).

   Multiple recipients can be given separated by commas.
