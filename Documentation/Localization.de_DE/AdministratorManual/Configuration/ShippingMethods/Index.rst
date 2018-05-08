.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Versandarten
============

Die Versandarten werden über TypoScript für jeden Warenkorb definiert. Das Standard-Template bringt bereits eine Versandart (Standard) mit.

Mit der Definition plugin.tx_cart.settings.allowedCountries wird die Ausgabe des Selektors im Warenkorb definiert. Durch einen eigenen SelectViewhelper können die Optionen auch übersetzt werden.

.. important::
   Das mitgelieferte TypoScript der Erweiterung bringt eine Konfiguration und Übersetzung für den deutschsprachigen Raum mit.

Der Parameter plugin.tx_cart.settings.defaultCountry definiert welches Land vorausgewählt werden soll.

.. important::
   Sollten für verschiedene Länder verschiedene Versandmethoden definiert sein, wird das Land der Lieferadresse genutzt. Nur in dem Fall, dass keine abweichende Lieferadresse angegeben wurde, wird die Auswahl der Rechnungsadresse zu Grunde gelegt.

Länderkonfiguration
"""""""""""""""""""

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
           countries {
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
   }

|

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.preset
   Data type
      int
   Description
      Definiert welche Versandart standardmäßig gewählt wird, sofern der Nutzer noch keine andere Versandart ausgewählt hat.
      Sollte beim Wechsel des Bestimmungslands die Versandmethode nicht definiert sein, wird ebenfalls die hier für das Bestimmungsland definierte Versandart ausgewählt.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n
   Data type
      array
   Description
      Man kann bis zu n verschiedene Versandarten konfigurieren.
   Default
      options.1

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.title
   Data type
      Text
   Description
      Name der Versandart (z.B.: Express).

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.extra
   Data type
      Text
   Description
      Kosten für die Versand, die dem Kunden in Rechnung gestellt werden sollen (z.B.: 1.50).
   Default
      0.00

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.free.from
   Data type
      Text
   Description
      Wenn der Bruttopreis der Produkte größer oder gleich dem angegebenen Wert ist, ist der Preis für die Versandart 0.00.
      Dies kann für Versandkostenfreiheit ab einem definierten Bestellwert genutzt werden.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.free.until
   Data type
      Text
   Description
      Wenn der Bruttopreis der Produkte kleiner oder gleich dem angegebenen Wert ist, ist der Preis für die Versandart 0.00.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.available.from
   Data type
      Text
   Description
      Nur wenn der Bruttopreis der Produkte größer oder gleich dem angegebenen Wert ist, ist diese Versandart verfügbar,
      anderfalls wird die Rückfall-Zahlmethode verwendet.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.available.until
   Data type
      Text
   Description
      Nur wenn der Bruttopreis der Produkte kleiner oder gleich dem angegebenen Wert ist, ist diese Versandart verfügbar,
      anderfalls wird die Rückfall-Zahlmethode verwendet.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.countries.de.options.n.available.fallBackId
   Data type
      Text
   Description
      Ist die Versandart nicht verfügbar, wird die Versandart mit dieser ID verwendet.

Zonenkonfiguration
""""""""""""""""""

Sollte keine individuelle Landeskonfiguration gefunden werden, kann auch mit Zonen (zones) im TypoScript gearbeitet werden.
Dies erspart jede Menge Konfigurationsarbeit, wenn in viele Länder geliefert werden soll.

::

   plugin.tx_cart {
       shippings {
           zones {
               1 {
                   preset = 1
                   countries = de,at,ch
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
       }
   }

|

.. container:: table-row

   Property
      plugin.tx_cart.shippings.zones.n
   Data type
      int
   Description
      Man kann bis zu n verschiedene Zonen konfigurieren.

.. container:: table-row

   Property
      plugin.tx_cart.shippings.zones.n.countries
   Data type
      int
   Description
      Liste der Länder, für die diese Konfiguration gültig ist.

.. NOTE::
   * Es wird erst in der Liste der Länderkonfiguration nach einer passenden Konfiguration gesucht.
   * Es wird dann die Liste der Zonenkonfigurationen durchgesehen. Die erste passende Konfiguration wird genutzt.

Versandarten deaktivieren
"""""""""""""""""""""""""

Im Moment erlaubt es die Verarbeitung nicht ganz auf die Versandarten zu verzichten. Eine Versandart muss immer angegeben
sein. Wenn es lediglich eine Versandart gibt, kann der Auswahlblock ausblendet werden.

::

   plugin.tx_cart {
        settings {
            showCartAction {
                showPartials {
                   shippingMethodForm = false
                }
            }
        }
   }

|

.. container:: table-row

   Property
      plugin.tx_cart.settings.showCartAction.showPartials.shippingMethodForm
   Data type
      boolean
   Description
      Aktiviert/Deaktiviert die Darstellung und Auswahl der konfigurierten Versandarten im Warenkorb.
   Default
      true
