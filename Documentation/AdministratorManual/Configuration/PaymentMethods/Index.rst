.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Bezahlmethoden
==============

Die Bezahlmethoden werden über TypoScript für jeden Warenkorb definiert. Das Standard-Template bringt bereits eine Bezahlmethode (Vorkasse) mit.

Mit der Definition plugin.tx_cart.settings.allowedCountries wird die Ausgabe des Selektors im Warenkorb definiert. Durch einen eigenen SelectViewhelper können die Optionen auch übersetzt werden.

:: important:
   Das mitgelieferte TypoScript der Erweiterung bringt eine Konfiguration und Übersetzung für den deutschsprachigen Raum mit.

Der Parameter plugin.tx_cart.settings.defaultCountry definiert welches Land vorausgewählt werden soll.

:: important:
   Sollten für verschiedene Länder verschiedene Bezahlmethoden definiert sein, wird die Auswahl der Rechnungsadresse für die erlaubten Bezahlmethoden genutzt. Das Land der Versandadresse, auch wenn eine abweichende Lieferadresse angegeben wurde, wird nicht genutzt.

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

       payments {
           de {
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
           at < .de
           ch < .de
       }
   }

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.preset
   Data type
      int
   Description
      Definiert welche Bezahlmethode standardmäßig gewählt wird, sofern der Nutzer noch keine ander Bezahlmethode ausgewählt hat.
      Sollte beim Wechsel des Rechnungslands die Bezahlmethode nicht definiert sein, wird ebenfalls die hier für das Rechnungsland definierte Zahlart ausgewählt.

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.1 … options.n
   Data type
      array
   Description
      Man kann bis zu n verschiedene Bezahlmethoden konfigurieren.
   Default
      options.1

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.n.title
   Data type
      Text
   Description
      Name der Bezahlmethode (z.B.: Nachnahme).

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.n.extra
   Data type
      Text
   Description
      Kosten für die Bezahlmethode, die dem Kunden in Rechnung gestellt werden sollen (z.B.: 1.50).
   Default
      0.00

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.n.free.from
   Data type
      Text
   Description
      Wenn der Bruttopreis der Produkte größer oder gleich dem angegebenen Wert ist, ist der Preis für die Bezahlmethode 0.00.

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.n.free.until
   Data type
      Text
   Description
      Wenn der Bruttopreis der Produkte kleiner oder gleich dem angegebenen Wert ist, ist der Preis für die Bezahlmethode 0.00.

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.n.available.from
   Data type
      Text
   Description
      Nur wenn der Bruttopreis der Produkte größer oder gleich dem angegebenen Wert ist, ist diese Bezahlmethode verfügbar,
      anderfalls wird die Rückfall-Zahlmethode verwendet.

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.n.available.until
   Data type
      Text
   Description
      Nur wenn der Bruttopreis der Produkte kleiner oder gleich dem angegebenen Wert ist, ist diese Bezahlmethode verfügbar,
      anderfalls wird die Rückfall-Zahlmethode verwendet.

.. container:: table-row

   Property
      plugin.tx_cart.payments.de.options.n.available.fallBackId
   Data type
      Text
   Description
      Ist die Bezahlmethode nicht verfügbar, wird die Zahlmethode mit dieser ID verwendet.
