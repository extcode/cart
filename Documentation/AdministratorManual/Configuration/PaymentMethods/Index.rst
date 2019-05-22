.. include:: ../../../Includes.txt

Payment methods
===============

The payment methods are defined via TypoScript for each shopping cart. The standard template already includes a payment method (prepayment).

With the definition plugin.tx_cart.settings.allowedCountries the output of the selector in the shopping cart is defined. The options can also be translated by an own SelectViewhelper.

.. important::
   The provided TypoScript of the extension provides a configuration and translation for the German-speaking area.

The parameter plugin.tx_cart.settings.defaultCountry defines which country should be preselected.

.. important::
   If different payment methods are defined for different countries, the selection of the invoice address is used for the permitted payment methods. The country of the shipping address, even if a different shipping address was specified, is not used.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.free.from
   Data type
      float
   Description
      If the gross price of the products is greater than or equal to the specified value, the price for the payment method is 0.00.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.free.until
   Data type
      float
   Description
      If the gross price of the products is less than or equal to the specified value, the price for the payment method is 0.00.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.available.fallBackId
   Data type
      int
   Description
      If the payment method is not available, the payment method with this ID is used.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.available.from
   Data type
      float
   Description
      This payment method is only available if the gross price of the products is greater than or equal to the specified value, otherwise the fallback payment method is used.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.available.until
   Data type
      float
   Description
      This payment method is only available if the gross price of the products is less than or equal to the specified value, otherwise the fallback payment method is used.

.. container:: table-row

   Property
      plugin.tx_cart.payments.countries.de.options.n.redirects.success.url
   Data type
      Text
   Description
      Ist für die genutzte Bezahlmethode einer Bestellung die Weiterleitungs-URL konfiguriert, wird nach erfolgreicher Bestellung auf die angegebene URL weitergeleitet statt die Bestätigungsseite anzuzeigen.

Zonenkonfiguration
""""""""""""""""""

Sollte keine individuelle Landeskonfiguration gefunden werden, kann auch mit Zonen (zones) im TypoScript gearbeitet werden.
Dies erspart jede Menge Konfigurationsarbeit, wenn in viele Länder geliefert werden soll.

::

   plugin.tx_cart {
       payments {
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
      plugin.tx_cart.payments.zones.n
   Data type
      int
   Description
      Man kann bis zu n verschiedene Zonen konfigurieren.

.. container:: table-row

   Property
      plugin.tx_cart.payments.zones.n.countries
   Data type
      int
   Description
      Liste der Länder, für die diese Konfiguration gültig ist.

.. NOTE::
   * Es wird erst in der Liste der Länderkonfiguration nach einer passenden Konfiguration gesucht.
   * Es wird dann die Liste der Zonenkonfigurationen durchgesehen. Die erste passende Konfiguration wird genutzt.

Bezahlmethoden deaktivieren
"""""""""""""""""""""""""""

Im Moment erlaubt es die Verarbeitung nicht ganz auf die Bezahlmethoden zu verzichten. Eine Bezahlmethoden muss immer angegeben
sein. Wenn es lediglich eine Bezahlmethode gibt, kann der Auswahlblock ausblendet werden.

::

   plugin.tx_cart {
        settings {
            showCartAction {
                showPartials {
                   paymentMethodForm = false
                }
            }
        }
   }

|

.. container:: table-row

   Property
      plugin.tx_cart.settings.showCartAction.showPartials.paymentMethodForm
   Data type
      boolean
   Description
      Aktiviert/Deaktiviert die Darstellung und Auswahl der konfigurierten Bezahlmethoden im Warenkorb.
   Default
      true

.. toctree::
   :maxdepth: 5
   :titlesonly:

   MainConfiguration/Index
