.. include:: ../../../../Includes.txt

..  _multi_step_checkout_configuration:

Multi-Step Checkout Configuration
=================================

The possibility of a multi-step checkout can be switched on in the extension via a configuration variable.
The default is still the one-step checkout. As an example, the extension provides templates for a four-step
checkout. The number of steps can be adjusted to individual needs.

::

   plugin.tx_cart {
       settings {
           cart {
               steps = 4
           }
       }
   }

.. container:: table-row

   Property
      plugin.tx_cart.settings.cart.steps
   Data type
      int
   Description
      If this configuration is set, the checkout will be divided into the specified number of slots.
      An HTML template file must then exist for each step.
      If this configuration is not set, the one-step checkout is used as before.

In the following, the provided example with 4 steps shall be explained, so that a customization is as easy as possible.
The four steps are divided as follows:

* Edit product list and enter voucher
* Enter billing address (shipping address if applicable)
* Select payment and shipping method
* Confirmation page with summary and checkboxes for the terms and conditions.

Each step has its own template under `Resources/Private/Templates/Cart/Cart` and is composed of ShowStep and the number
of the step (e.g.: ShowStep1.html, ShowStep2.html etc.).
The steps use the existing partials as far as possible, so that a conversion should be easy, or even a conditional
output is possible. For example, a multi-step checkout could be used on mobile devices to improve clarity, and a
one-step checkout could be used on devices with a large resolution.