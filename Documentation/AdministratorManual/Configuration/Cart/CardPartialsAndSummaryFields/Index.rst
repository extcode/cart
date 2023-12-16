.. include:: ../../../../Includes.txt

================================
Cart partials and summary fields
================================

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



plugin.tx_cart.settings.showCartAction.showPartials
===================================================

.. _plugin_tx_cart_settings_showCartAction_showPartials_paymentMethodForm:

.. confval:: paymentMethodForm

   :Type: boolean
   :Default: true

   Enables/disables the display and selection of configured payment methods
   in the shopping cart.

.. _plugin_tx_cart_settings_showCartAction_showPartials_shippingMethodForm:

.. confval:: shippingMethodForm

   :Type: boolean
   :Default: true

   Enables/disables the display and selection of configured shipping methods
   in the shopping cart.

.. confval:: couponForm

   :Type: boolean
   :Default: true

   Enables/disables the display of coupons in the shopping cart. The block
   for the input as well as for the display will be shown/hidden.

