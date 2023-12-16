.. include:: ../../../Includes.txt

===============
Currency Format
===============

To make the templates a bit easier to use, TypoScript can be used to configure
the format specifications for the price output.
These TypoScript specifications are then used in the <cart:format.currency>
ViewHelper.

.. code-block:: typoscript

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
