.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Vesandarten
===========

Die Versandarten werden über TypoScript für jeden Warenkorb definiert. Das Standard-Template bringt bereits eine
Versandart (Standard) mit.

::

   plugin.tx_cart {
       shippings {
           preset = 1
           options {
               1 {
                   title = Standard
                   extra = 0.00
                   taxClassId = 1
                   status = open
               }
           }
       }
   }

.. container:: table-row

   Property
      shippings.preset
   Data type
      int
   Description
      Definiert welche Versandart standardmäßig gewählt wird, sofern der Nutzer noch keine ander Versandart ausgewählt hat.

.. container:: table-row

   Property
      shippings.options.1 … options.n
   Data type
      array
   Description
      Man kann bis zu n verschiedene Versandarten konfigurieren.
   Default
         options.1

.. container:: table-row

   Property
      shippings.options.n.title
   Data type
      Text
   Description
      Name der Versandart (z.B.: Express).

.. container:: table-row

   Property
      shippings.options.n.extra
   Data type
      Text
   Description
      Kosten für die Versand, die dem Kunden in Rechnung gestellt werden sollen (z.B.: 1.50).
   Default
      0.00

.. container:: table-row

   Property
      shippings.options.n.free\_from
   Data type
      Text
   Description
      Wenn der Bruttopreis der Produkte größer oder gleich dem angegebenen Wert ist, ist der Preis für die Versandart 0.00.
      Dies kann für Versandkostenfreiheit ab einem definierten Bestellwert genutzt werden.
