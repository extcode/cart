.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Hauptkonfiguration
==================

settings.cart.pid
"""""""""""""""""
.. container:: table-row

   Property
      settings.cart.pid
   Data type
      string
   Description
      Definiert die Seite in der das Warenkorb-Plugin liegt. Dies wird benötigt, um ein Produkt dem richtigen
      Warenkorb zuzuorden und auf den Warenkorb zu verweisen.

settings.cart.isNetCart
"""""""""""""""""""""""
.. container:: table-row

   Property
      settings.cart.isNetCart
   Data type
      boolean
   Description
      Definiert ob der Warenkorb als Nettowarenkorb behandelt werden soll. Ist der Warenkorb ein Nettowarenkorb,
      werden die Preisberechungen alle auf den Nettopreisen der Produkte durchgeführt und ausgegeben, andernfalls
      erfolgt die Berechnungn mit den Bruttopreisen.

settings.order.pid
""""""""""""""""""
.. container:: table-row

   Property
      settings.order.pid
   Data type
      string
   Description
      Definiert die Seite/den Ordner in der die Bestellungen gespeichert werden sollen.

settings.addToCartByAjax
""""""""""""""""""""""""
.. container:: table-row

   Property
      settings.addToCartByAjax
   Data type
      boolean
   Description
      Schaltet das Hinzufügen von Produkten zum Warenkorb via AJAX ein. Eine Weiterleitung zum Warenkorb erfolgt dann
      mit dem mitgelieferten JavaScript nicht.
