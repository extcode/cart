.. include:: ../../../../Includes.txt
.. _adminstration_order_mail:
===============
Email addresses
===============

Email addresses must be configured for sending emails. This can be done in
the backend via the plugin, but also configured via TypoScript.

.. code-block:: typoscript
   :caption: Can be set in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       mail {
           buyer {
               fromAddress = cart.buyer.sender@example.com
               ccAddress = cart.buyer.cc1@example.com, cart.buyer.cc2@example.com
               bccAddress = cart.buyer.bcc1@example.com, cart.buyer.bcc2@example.com
               replyToAddress = cart.buyer.reply@example.com
               attachments {
                   1 = EXT:sitepackage/Resources/Public/Files/AGB.pdf
               }
           }
           seller {
               fromAddress = cart.seller.sender@example.com
               toAddress = cart.seller.to1@example.com, cart.seller.to2@example.com
               ccAddress = cart.seller.cc1@example.com, cart.seller.cc2@example.com
               bccAddress = cart.seller.bcc1@example.com, cart.seller.bcc2@example.com
           }
       }
   }

plugin.tx_cart.mail
===================

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

.. confval:: buyer.attachments.<n>

   :Type: array

   Defines one or more email attachments to be sent to the buyer.

   These can be, for example, documents with the general terms and conditions.

.. confval:: buyer.attachDocuments.<n>

   :Type: array

   Defines one or more email attachments of the generated PDF documents to be
   sent to the buyer.

   This can be the order confirmation, the invoice or a separate document.

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

.. confval:: seller.attachments.<n>

   :Type: array

   Defines one or more email attachments to be sent to the seller.

   These can be, for example, documents with the general terms and conditions.

.. confval:: seller.attachDocuments.<n>

   :Type: array

   Defines one or more email attachments of the generated PDF documents to be
   sent to the seller.

   This can be the order confirmation, the invoice or a separate document.
