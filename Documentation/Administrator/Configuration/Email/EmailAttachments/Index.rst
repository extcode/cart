.. include:: ../../../../Includes.rst.txt

.. _mail_attachments:

=================
Email attachments
=================

Email attachments can be configured as shown below.

The attachments of the emails to the buyer and to the seller are added to the
emails at the end of an order and to the emails on status changes, see
:ref:`status-change-emails`. They are read from TypoScript, so they are not
available on the command line.

.. code-block:: typoscript
   :caption: Can be set in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       mail {
           // Used for emails sent to the customer (=buyer)
           buyer {
               attachments {
                   1 = EXT:sitepackage/Resources/Public/Files/AGB.pdf
               }
               // This needs EXT:cart_pdf to work!
               attachDocuments {
                  order = 1
                  invoice = 1
                  delivery = 1
               }
           }

           // Used for emails sent to the shop owner (=seller)
           seller {
               attachments {
                   1 = EXT:sitepackage/Resources/Public/Files/AGB.pdf
               }
               // This needs EXT:cart_pdf to work!
               attachDocuments {
                  order = 1
                  invoice = 1
                  delivery = 1
               }
           }
       }
   }

plugin.tx_cart.mail
===================

.. confval:: buyer.attachments.<n>

   :Type: array

   Defines one or more email attachments to be sent to the buyer.

   These can be, for example, documents with the general terms and conditions.

.. confval:: buyer.attachDocuments.<n>

   :Type: array

   **This needs EXT:cart_pdf to work.**

   Defines one or more email attachments of the generated PDF documents to be
   sent to the buyer.

   This can be the order confirmation, the invoice or a separate document.

.. confval:: seller.attachments.<n>

   :Type: array

   Defines one or more email attachments to be sent to the seller.

   These can be, for example, documents with the general terms and conditions.

.. confval:: seller.attachDocuments.<n>

   :Type: array

   **This needs EXT:cart_pdf to work.**

   Defines one or more email attachments of the generated PDF documents to be
   sent to the seller.

   This can be the order confirmation, the invoice or a separate document.
