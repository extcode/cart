.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

showCartAction-Konfiguration
============================

::

   plugin.tx_cart {
       settings {
           showCartAction {
               validation {
                   fields {
                       acceptTerms = true
                       acceptConditions = true
                   }
               }
               summary {
                   fields {
                       cart.net = true
                       cart.taxes = true
                       discount.gross = true
                       service.gross = true
                       total.gross = true
                   }
               }
           }
       }
   }

settings.showCartAction.validation.fields.acceptTerms
"""""""""""""""""""""""""""""""""""""""""""""""""""""
.. container:: table-row

   Property
      settings.showCartAction.validation.fields.acceptTerms
   Data type
      string
   Description
      Schaltet die Checkbox "AGB akzeptieren" und die Validierung aus/ein.

settings.showCartAction.validation.fields.acceptConditions
""""""""""""""""""""""""""""""""""""""""""""""""""""""""""
.. container:: table-row

   Property
      settings.showCartAction.validation.fields.acceptConditions
   Data type
      string
   Description
      Schaltet die Checkbox "Widerrufsbelehrung akzeptieren" und die Validierung aus/ein.
