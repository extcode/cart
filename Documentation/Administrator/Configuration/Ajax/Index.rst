.. include:: ../../../Includes.rst.txt

====
AJAX
====

To add products to the shopping cart via AJAX request, a configuration for a
custom page type is needed, because in this case you don't want to receive the
completely rendered page as response, but only a JSON object.

The default templates are preconfigured for AJAX and the extension also
contains the needed JavaScript.

..  tip::
    When using own templates and partials you need
    to make sure that the needed HTML classes and IDs are given if you want
    to use the JavaScript delivered by this extension.

This is the supplied configuration for the shopping cart plugin:

.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       settings {
           addToCartByAjax = 2278001
       }
   }

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

       10 = EXTBASEPLUGIN
       10 {
           vendorName = Extcode
           extensionName = Cart
           pluginName = Cart
           controller = Product
       }
   }


plugin.tx_cart.settings
=======================

.. confval:: addToCartByAjax

   :Type: int
   :Default: 2278001

   Activates the option to add products via AJAX action. There is no
   forwarding to the shopping cart page. The value is used as typeNum in
   the default templates of `extcode/cart-books`, `extcode/cart-events`,
   `extcode/cart-products` and other product extensions.

   The response can used to display messages or update the MiniCart plugin.
