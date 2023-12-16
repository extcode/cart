.. include:: ../../../Includes.txt

====
AJAX
====

To add products to the shopping cart via AJAX request, a configuration for a
custom page type is needed, because in this case you don't want to receive the
completely rendered page as response, but only a JSON object,

.. code-block:: typoscript

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

This is the supplied configuration for the shopping cart plugin. Products can
be added to the shopping cart via this page type (2278001).
