.. include:: ../../../../Includes.txt

Email Configuration
====================

Email addresses must be configured for sending emails. This can be done in the backend via the plugin,
but also configured via TypoScript.

::

   plugin.tx_cart {
       mail {
           buyer {
               fromAddress = cart.buyer.sender@example.com
               ccAddress = cart.buyer.cc1@example.com, cart.buyer.cc2@example.com
               bccAddress = cart.buyer.bcc1@example.com, cart.buyer.bcc2@example.com
               replyToAddress = cart.buyer.reply@example.com
               attachments {
                   1 = EXT:theme_cart/Resources/Public/Files/AGB.pdf
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

.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.fromAddress
   Data type
      string
   Description
      Defines from which sender address the e-mails are sent to the buyer.

.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.ccAddress
   Data type
      string
   Description
      Defines to which addresses the e-mail should be sent in CC (Carbon Copy).
      Multiple recipients can be given separated by commas.

.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.bccAddress
   Data type
      string
   Description
      Defines to which addresses the e-mail should be sent in BCC (Blind Carbon Copy).
      Multiple recipients can be given separated by commas.

.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.replyToAddress
   Data type
      string
   Description
      Defines to which address should be used as Reply To.
      This will override the ['MAIL']['defaultMailReplyToAddress'] configuration.

.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.attachments.n
   Data type
      array
   Description
      Defines one or more e-mail attachments to be sent to the buyer.
      These can be, for example, documents with the general terms and conditions.

.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.attachDocuments.n
   Data type
      array
   Description
      Defines one or more e-mail attachments of the generated PDF documents to be sent to the buyer.
      This can be the order confirmation, the invoice or a separate document.

.. container:: table-row

   Property
      plugin.tx_cart.mail.seller.fromAddress
   Data type
      string
   Description
      Defines from which sender address the e-mails are sent to the seller/shop operator.

.. container:: table-row

   Property
      plugin.tx_cart.mail.seller.toAddress
   Data type
      string
   Description
      Defines to which recipient addresses the e-mails to the seller/shop operator are sent.
      Multiple recipients can be given separated by commas.

.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.ccAddress
   Data type
      string
   Description
      Defines to which addresses the e-mail should be sent in CC (Carbon Copy).
      Multiple recipients can be given separated by commas.

.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.bccAddress
   Data type
      string
   Description
      Defines to which addresses the e-mail should be sent in BCC (Blind Carbon Copy).
      Multiple recipients can be given separated by commas.

.. container:: table-row

   Property
      plugin.tx_cart.mail.seller.attachments.n
   Data type
      array
   Description
      Defines one or more e-mail attachments to be sent to the seller.
      These can be, for example, documents with the general terms and conditions.

.. container:: table-row

   Property
      plugin.tx_cart.mail.seller.attachDocuments.n
   Data type
      array
   Description
      Defines one or more e-mail attachments of the generated PDF documents to be sent to the seller.
      This can be the order confirmation, the invoice or a separate document.
