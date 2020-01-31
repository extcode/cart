.. include:: ../../../Includes.txt

Main Configuration
==================

::

   plugin.tx_cart {
       settings {
           cart {
               pid =
               isNetCart =
           }
           order {
               pid =
           }

           addToCartByAjax
       }
   }

settings.cart.pid
"""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.cart.pid
   Data type
      string
   Description
      Defines the page where the cart plugin is located. This is needed to put products in the right shopping cart. If
      settings.addToCartByAjax isn't set, the add to cart action will forwards the user to this page.

settings.cart.isNetCart
"""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.cart.isNetCart
   Data type
      boolean
   Description
      Defines whether the shopping cart should be treated as a net shopping cart. If the shopping cart is a net
      shopping cart, the price calculations are all carried out and displayed on the net prices of the products,
      otherwise the calculations are made with the gross prices.

settings.order.pid
""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cart.settings.order.pid
   Data type
      string
   Description
      Specifies the folder in which the orders should be stored.

settings.addToCartByAjax
""""""""""""""""""""""""
.. container:: table-row

   Property
      plugin.tx_cartproducts.settings.addToCartByAjax
   Data type
      int
   Description
      Activates the option to add products via AJAX action. There is no forwarding to the shopping cart page.
      The response can used to display messages or update the MiniCart-Plugin.