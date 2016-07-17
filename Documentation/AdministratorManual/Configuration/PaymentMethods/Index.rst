.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Bezahlmethoden
==============

Die Bezahlmethoden werden über TypoScript für jeden Warenkorb definiert. Das Standard-Template bringt bereits eine
Bezahlmethode (Vorkasse) mit.

::

   plugin.tx_cart {
       payments {
           preset = 1
           options {
               1 {
                   title = Vorkasse
                   extra = 0.00
                   taxClassId = 1
                   status = open
               }
           }
       }
   }


.. container:: table-row

   Property
      payments.preset
   Data type
      int
   Description
      Definiert welche Bezahlmethode standardmäßig gewählt wird, sofern der Nutzer noch keine ander Bezahlmethode ausgewählt hat.

.. container:: table-row

   Property
      payments.options.1 … options.n
   Data type
      array
   Description
      Man kann bis zu n verschiedene Bezahlmethoden konfigurieren.
   Default
         options.1

.. container:: table-row

   Property
      payments.options.n.title
   Data type
      Text
   Description
      Name der Bezahlmethode (z.B.: Nachnahme).

.. container:: table-row

   Property
      payments.options.n.extra
   Data type
      Text
   Description
      Kosten für die Bezahlmethode, die dem Kunden in Rechnung gestellt werden sollen (z.B.: 1.50).
   Default
      0.00

.. container:: table-row

   Property
      payments.options.n.free\_from
   Data type
      Text
   Description
      Wenn der Bruttopreis der Produkte größer oder gleich dem angegebenen Wert ist, ist der Preis für die Bezahlmethode 0.00.
