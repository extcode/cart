.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Vesandarten
===========

Die Versandarten werden über TypoScript für jeden Warenkorb definiert. Das Standard-Template bringt bereits eine Versandart (Standard) mit.

Mit der Definition plugin.tx_cart.settings.allowedCountries wird die Ausgabe des Selektors im Warenkorb definiert. Durch einen eigenen SelectViewhelper können die Optionen auch übersetzt werden.

:: important:
   Das mitgelieferte TypoScript der Erweiterung bringt eine Konfiguration und Übersetzung für den deutschsprachigen Raum mit.

Der Parameter plugin.tx_cart.settings.defaultCountry definiert welches Land vorausgewählt werden soll.

:: important:
   Sollten für verschiedene Länder verschiedene Versandmethoden definiert sein, wird das Land der Lieferadresse genutzt. Nur in dem Fall, dass keine abweichende Lieferadresse angegeben wurde, wird die Auswahl der Rechnungsadresse zu Grunde gelegt.

::

   plugin.tx_cart {
       settings {
           allowedCountries {
               de = Deutschland
               at = Österreich
               ch = Schweiz
           }
           defaultCountry = de
       }

       shippings {
           de {
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
           at < .de
           ch < .de
       }
   }


Im folgenden

.. container:: table-row

   Property
      plugin.tx_cart.shippings.de.preset
   Data type
      int
   Description
      Definiert welche Versandart standardmäßig gewählt wird, sofern der Nutzer noch keine andere Versandart ausgewählt hat.
      Sollte beim Wechsel des Bestimmungslands die Versandmethode nicht definiert sein, wird ebenfalls die hier für das Bestimmungsland definierte Versandart ausgewählt.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.de.options.1 … options.n
   Data type
      array
   Description
      Man kann bis zu n verschiedene Versandarten konfigurieren.
   Default
      options.1

.. container:: table-row

   Property
      plugin.tx_cart.shippings.de.options.n.title
   Data type
      Text
   Description
      Name der Versandart (z.B.: Express).

.. container:: table-row

   Property
      plugin.tx_cart.shippings.de.options.n.extra
   Data type
      Text
   Description
      Kosten für die Versand, die dem Kunden in Rechnung gestellt werden sollen (z.B.: 1.50).
   Default
      0.00

.. container:: table-row

   Property
      plugin.tx_cart.shippings.de.options.n.free.from
   Data type
      Text
   Description
      Wenn der Bruttopreis der Produkte größer oder gleich dem angegebenen Wert ist, ist der Preis für die Versandart 0.00.
      Dies kann für Versandkostenfreiheit ab einem definierten Bestellwert genutzt werden.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.de.options.n.free.until
   Data type
      Text
   Description
      Wenn der Bruttopreis der Produkte kleiner oder gleich dem angegebenen Wert ist, ist der Preis für die Versandart 0.00.

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.n.available.from
   Data type
      Text
   Description
      Nur wenn der Bruttopreis der Produkte größer oder gleich dem angegebenen Wert ist, ist diese Versandart verfügbar,
      anderfalls wird die Rückfall-Zahlmethode verwendet.

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.n.available.until
   Data type
      Text
   Description
      Nur wenn der Bruttopreis der Produkte kleiner oder gleich dem angegebenen Wert ist, ist diese Versandart verfügbar,
      anderfalls wird die Rückfall-Zahlmethode verwendet.

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.n.available.fallBackId
   Data type
      Text
   Description
      Ist die Versandart nicht verfügbar, wird die Versandart mit dieser ID verwendet.
