.. include:: ../../../Includes.txt

Payment and Shipping methods
============================

The configuration of payment and shipping methods is largely identical. They only differ in some options.
Therefore, the configuration for *ServiceMethod* is described. In case a configuration is only available
for one service type, only this one will be described. In your own configuration the *ServiceMethod* should
be replaced by `payment` or `shipping`.

Example
^^^^^^^
The description

.. container:: table-row

   Property
      plugin.tx_cart.ServiceMethod.countries.de.preset
   Data type
      int
   Description
      Defines which *ServiceMethod* is selected by default if the user has not yet selected a different *ServiceMethod*.
      If the *ServiceMethod* is not defined when changing the country of account, the *ServiceMethod* defined here for the country of invoice will also be selected.

can be used for both `payment` and `shipping`

.. code-block:: typoscript

   # ServiceMethod = payment
   plugin.tx_cart.payment.countries.de.preset = 1

   # PaymentMethod = shipping
   plugin.tx_cart.shipping.countries.de.preset = 1
