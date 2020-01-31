.. include:: ../../../Includes.txt

Format Konfiguration
====================

Um die Templates etwas einfacher zu gestalten, können über TypoScript die Formatangaben für die Preisausgabe konfiguriert
werden. Diese TypoScript-Angaben werden dann im <cart:format.currency>-ViewHelper verwendet.

::

   plugin.tx_cart {
       settings {
           format {
               currency {
                   currencySign       = &euro;
                   decimalSeparator   = ,
                   thousandsSeparator = .
                   prependCurrency    = false
                   separateCurrency   = true
                   decimals           = 2
               }
           }
       }
   }
