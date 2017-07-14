.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

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

plugin.tx_cart.settings.currency.default
""""""""""""""""""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.default
   Data type
      int
   Description
      Definiert mit welche die Standardwährung eines neuen Warenkorbs sein soll.
   Default
      1

plugin.tx_cart.settings.currency.1 .. n
"""""""""""""""""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.1
   Data type
      array
   Description
      Liste der verschiedenen verfügbaren Währungen

plugin.tx_cart.settings.currency.1.code .. n.code
"""""""""""""""""""""""""""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.1.code
   Data type
      array
   Description
      Dreistelliger internationaler Währungscode nach `ISO 4217 (Wikipedia) <https://de.wikipedia.org/wiki/ISO_4217>`_. Dieser wird unter anderem für verschiedene
      Zahlungsanbieter benötigt und als Parameter für die Änderung der Währung im Warenkorb benötigt.
   Default
      EUR

plugin.tx_cart.settings.currency.1.sign .. n.sign
"""""""""""""""""""""""""""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.1.sign
   Data type
      array
   Description
      Währungssymbol, falls für die Währung vorhanden.
   Default
      €

plugin.tx_cart.settings.currency.1.translation .. n.translation
"""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.currency.1.translation
   Data type
      array
   Description
      Währungsumrechnungsfaktor
   Default
      1.0
