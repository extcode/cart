.. include:: ../../../../../Includes.txt

============================
showCartAction Configuration
============================

.. code-block:: typoscript

   plugin.tx_cart {
       settings {
           showCartAction {
               showPartials {
                   couponForm = true

                   # if setting shippingAddressForm to false please disable all validations too
                   shippingAddressForm = true

                   shippingMethodForm = true
                   paymentMethodForm = true
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

.. container:: table-row

   Property
      plugin.tx_cart.settings.showCartAction.showPartials.paymentMethodForm
   Data type
      boolean
   Description
      Enables/disables the display and selection of configured payment methods
      in the shopping cart.
   Default
      true


.. container:: table-row

   Property
      plugin.tx_cart.settings.showCartAction.showPartials.shippingMethodForm
   Data type
      boolean
   Description
      Enables/disables the display and selection of configured shipping methods
      in the shopping cart.
   Default
      true

.. container:: table-row

   Property
      plugin.tx_cart.settings.showCartAction.showPartials.couponForm
   Data type
      boolean
   Description
      Enables/disables the display of coupons in the shopping cart. The block
      for the input as well as for the and for the display will be shown/hidden.
   Default
      true
