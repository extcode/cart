.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

E-Mail-Konfiguration
====================

Für den Versand der E-Mails müssen E-Mail-Adressen konfiguriert werden. Das kann im Backend über das Plugin geschehen,
aber auch über TypoScript konfiguriert werden.

::

   plugin.tx_cart {
       mail {
           buyer {
               fromAddress = cart.buyer.sender@extco.de
               attachments {
                   1 = EXT:theme_cart/Resources/Public/Files/AGB.pdf
               }
           }
           seller {
               fromAddress = cart.seller.sender@extco.de
               toAddress = cart.seller.receiver@extco.de
           }
       }
   }

mail.buyer.fromAddress
""""""""""""""""""""""
.. container:: table-row

   Property
      mail.buyer.fromAddress
   Data type
      string
   Description
      Definiert mit welcher Absender-Adresse die E-Mails an den Käufer gesendet werden.

mail.buyer.attachments
""""""""""""""""""""""
.. container:: table-row

   Property
      mail.buyer.attachments.n
   Data type
      array
   Description
      Definiert einen E-Mail-Anhang/mehrere E-Mail-Anhänge die an den Käufer geschickt werden. Dies können zum Beispiel Dokumente mit den Allgemeinen Geschäftsbedingungen sein.

mail.buyer.attachDocuments
""""""""""""""""""""""""""
.. container:: table-row

   Property
      mail.buyer.attachDocuments.n
   Data type
      array
   Description
      Definiert einen E-Mail-Anhang/mehrere E-Mail-Anhänge der generierten PDF-Dokumente die an den Käufer geschickt werden. Dies kann die Bestellbestätigung, die Rechnung oder ein eigenes Dokument sein.

mail.seller.fromAddress
"""""""""""""""""""""""
.. container:: table-row

   Property
      mail.seller.fromAddress
   Data type
      string
   Description
      Definiert mit welcher Absender-Adresse die E-Mails an den Verkäufer/Shopbetreiber gesendet werden.

mail.seller.toAddress
"""""""""""""""""""""
.. container:: table-row

   Property
      mail.seller.toAddress
   Data type
      string
   Description
      Definiert an welche Empfänger-Adressen die E-Mails an den Verkäufer/Shopbetreiber gesendet werden. Sollen die E-Mails an mehrere Empfänger gehen, können diese kommasepariert angegeben werden.

mail.seller.attachments
"""""""""""""""""""""""
.. container:: table-row

   Property
      mail.seller.attachments.n
   Data type
      array
   Description
      Definiert einen E-Mail-Anhang/mehrere E-Mail-Anhänge die an den Verkäufer/Shopbetreiber geschickt werden.

mail.seller.attachDocuments
"""""""""""""""""""""""""""
.. container:: table-row

   Property
      mail.seller.attachDocuments.n
   Data type
      array
   Description
      Definiert einen E-Mail-Anhang/mehrere E-Mail-Anhänge der generierten PDF-Dokumente die an den Verkäufer/Shopbetreiber geschickt werden. Dies kann die Bestellbestätigung, die Rechnung, der Lieferschein oder ein eigenes Dokument sein.