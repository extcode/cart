.. include:: ../../../Includes.txt

AJAX Konfiguration
==================

Um Produkte per AJAX-Request in den Warenkorb zu legen, wird eine Konfiguration für einen eigenen Seitentyp benötigt, denn in diesem Fall möchte man nicht die komplett gerenderte Seite als Antwort erhalten, sondern lediglich ein JSON-Objekt,

::

   ajaxCart = PAGE
   ajaxCart {
       typeNum = 2278001

       config {
           disableAllHeaderCode = 1
           xhtml_cleaning = 0
           admPanel = 0
           debug = 0
           no_cache = 1
       }

       10 < tt_content.list.20.cart_cart
   }

Dies ist die mitgelieferte Konfiguration für das Warenkorb-Plugin. Über diesen Seitentyp (2278001) lassen sich Produkte zum Warenkorb hinzufügen.
