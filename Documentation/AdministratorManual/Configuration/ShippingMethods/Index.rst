.. include:: ../../../Includes.txt

Shipping methods
================

The shipping methods are defined via TypoScript for each shopping cart. The standard template already comes with a shipping method (standard).

With the definition plugin.tx_cart.settings.allowedCountries the output of the selector in the shopping cart is defined. The options can also be translated by an own SelectViewhelper.

.. important::
   The provided TypoScript of the extension provides a configuration and translation for the German-speaking area.

The parameter plugin.tx_cart.settings.defaultCountry defines which country should be preselected.

.. important::
   If different shipping methods are defined for different countries, the country of the delivery address is used. Only in the case that no different delivery address has been specified, the selection of the billing address is used as a basis.

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

.. toctree::
   :maxdepth: 5
   :titlesonly:

   MainConfiguration/Index
   FlexPrices/Index
