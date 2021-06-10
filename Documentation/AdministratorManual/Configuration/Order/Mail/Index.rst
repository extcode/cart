.. include:: ../../../../Includes.txt

Email Configuration
====================

Email addresses must be configured for sending emails. This can be done in the backend via the plugin,
but also configured via TypoScript.

::

   plugin.tx_cart {
       mail {
           buyer {
               fromAddress = cart.buyer.sender@extco.de
               bccAddress = cart.buyer.cc1@extco.de, cart.buyer.cc2@extco.de
               bccAddress = cart.buyer.bcc1@extco.de, cart.buyer.bcc2@extco.de
               replyToAddress = cart.buyer.reply@extco.de
               attachments {
                   1 = EXT:theme_cart/Resources/Public/Files/AGB.pdf
               }
           }
           seller {
               fromAddress = cart.seller.sender@extco.de
               toAddress = cart.seller.to1@extco.de, cart.seller.to2@extco.de
               bccAddress = cart.seller.cc1@extco.de, cart.seller.cc2@extco.de
               bccAddress = cart.seller.bcc1@extco.de, cart.seller.bcc2@extco.de
           }
       }
   }

mail.buyer.fromAddress
""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.fromAddress
   Data type
      string
   Description
      Defines from which sender address the e-mails are sent to the buyer.

mail.buyer.ccAddress
"""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.ccAddress
   Data type
      string
   Description
      Defines to which addresses the e-mail should be sent in CC (Carbon Copy).
      Multiple recipients can be given separated by commas.

mail.buyer.bccAddress
"""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.bccAddress
   Data type
      string
   Description
      Defines to which addresses the e-mail should be sent in BCC (Blind Carbon Copy).
      Multiple recipients can be given separated by commas.

mail.buyer.replyToAddress
"""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.replyToAddress
   Data type
      string
   Description
      Defines to which address should be used as Reply To.
      This will override the ['MAIL']['defaultMailReplyToAddress'] configuration.

mail.buyer.attachments
""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.attachments.n
   Data type
      array
   Description
      Defines one or more e-mail attachments to be sent to the buyer.
      These can be, for example, documents with the general terms and conditions.

mail.buyer.attachDocuments
""""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.attachDocuments.n
   Data type
      array
   Description
      Defines one or more e-mail attachments of the generated PDF documents to be sent to the buyer.
      This can be the order confirmation, the invoice or a separate document.

mail.seller.fromAddress
"""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.seller.fromAddress
   Data type
      string
   Description
      Defines from which sender address the e-mails are sent to the seller/shop operator.

mail.seller.toAddress
"""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.seller.toAddress
   Data type
      string
   Description
      Defines to which recipient addresses the e-mails to the seller/shop operator are sent.
      Multiple recipients can be given separated by commas.

mail.seller.ccAddress
"""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.ccAddress
   Data type
      string
   Description
      Defines to which addresses the e-mail should be sent in CC (Carbon Copy).
      Multiple recipients can be given separated by commas.

mail.seller.bccAddress
"""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.buyer.bccAddress
   Data type
      string
   Description
      Defines to which addresses the e-mail should be sent in BCC (Blind Carbon Copy).
      Multiple recipients can be given separated by commas.

mail.seller.attachments
"""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.seller.attachments.n
   Data type
      array
   Description
      Defines one or more e-mail attachments to be sent to the buyer.
      These can be, for example, documents with the general terms and conditions.

mail.seller.attachDocuments
"""""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.mail.seller.attachDocuments.n
   Data type
      array
   Description
      Defines one or more e-mail attachments of the generated PDF documents to be sent to the buyer.
      This can be the order confirmation, the invoice or a separate document.
