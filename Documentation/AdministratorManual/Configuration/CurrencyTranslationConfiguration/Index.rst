.. include:: ../../../Includes.txt

Währungsumrechnung
==================

Möchte man im Shop dem Nutzer erlauben die Preise in einer anderen Währung anzeigen zu lassen, kann man diese über
TypoScript konfigurieren.

::

   plugin.tx_cart {
       settings {
           currencies {
               default = 1
               1 {
                   code = EUR
                   sign = €
                   translation = 1.00
               }
           }
       }
   }

settings.currency.default
"""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.default
   Data type
      int
   Description
      Definiert mit welche die Standardwährung eines neuen Warenkorbs sein soll.
   Default
      1

settings.currency.n
"""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.n
   Data type
      array
   Description
      Liste der verschiedenen verfügbaren Währungen

settings.currency.n.code
""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.n.code
   Data type
      array
   Description
      Dreistelliger internationaler Währungscode nach `ISO 4217 (Wikipedia) <https://de.wikipedia.org/wiki/ISO_4217>`_. Dieser wird unter anderem für verschiedene
      Zahlungsanbieter benötigt und als Parameter für die Änderung der Währung im Warenkorb benötigt.
   Default
      EUR

settings.currency.n.sign
""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.n.sign
   Data type
      array
   Description
      Währungssymbol, falls für die Währung vorhanden.
   Default
      €

currency.n.translation
""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.n.translation
   Data type
      array
   Description
      Währungsumrechnungsfaktor. Der Preis der Produkte wird durch diesen Faktor geteilt.
   Default
      1.0

.. NOTE::
   Im Moment gibt es noch keinen Automatismus den Faktor zu aktualisieren und an einen aktuellen Wert anzupassen. Mit
   einem kleinen Scheduler-Task sollte aber recht schnell ein entsprechender Service angebunden werden können.
