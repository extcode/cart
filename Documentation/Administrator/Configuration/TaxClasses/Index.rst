.. include:: ../../../Includes.txt

===========
Tax classes
===========

Tax classes can be adapted, added and removed via TypoScript.
Three classes are predefined:

.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/constants.typoscript

   plugin.tx_cart {
       taxClasses {
           1 {
               value = 19
               calc = 0.19
               name = normal
           }
           2 {
               value = 7
               calc = 0.07
               name = reduced
           }
           3 {
               value = 0
               calc = 0.00
               name = free
           }
       }
   }

Tax classes can also depend on countries:

.. code-block:: typoscript
   :caption: e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript (not in constants.typoscript!)

   plugin.tx_cart {
       // unset predefined tax classes
       taxClasses >
       taxClasses {
           de {
               1 {
                   value = 19
                   calc = 0.19
                   name = normal
               }
               2 {
                   value = 7
                   calc = 0.07
                   name = reduced
               }
           }
           at {
               1 {
                   value = 20
                   calc = 0.2
                   name = normal
               }
               2 {
                   value = 10
                   calc = 0.1
                   name = reduced
               }
           }
           // 'fallback' will be used if the country chosen by the customer is
           // not defined above.
           // It is of course not recommended to make use of it!
           fallback {
               1 {
                   value = 15
                   calc = 0.15
                   name = normal
               }
               2 {
                   value = 7
                   calc = 0.07
                   name = reduced
               }
           }
       }
   }
