.. include:: ../../Includes.txt

================================================================
Breaking: #242 - Use defaultCountry setting for shipping address
================================================================

See :issue:`242`

Description
===========

The defaultCountry TypoScript configuration will applied to the shipping address too.
The old logic uses an empty shipping country as an indicator that no shipping address should be used.
The new logic uses the new attribute `shippingSameAsBilling` within the cart model.
Some fluid templates and JavaScript functions have been changed.


Impact
======

Using the old templates causes the problem that the shipping address will not be hidden and has to be
filled out by the customer.


Affected Installations
======================

Instances which use custom partial templates for:

:file:`EXT:cart/Resources/Private/Partials/Cart/OrderForm.html`
:file:`EXT:cart/Resources/Private/Partials/Cart/OrderForm/Address/Shipping.html`

or own JavaScript instead of

:file:`EXT:cart/Resources/Public/JavaScripts/cart.js`.


Migration
=========

Replace all conditions on `{cart.shippingCountry}` in both templates.
Replace the attribute :html:`disable='true'` in all input fields of the shipping address template
with :html:`disabled="{f:if(condition:'{cart.shippingSameAsBilling}', then:'true')}"`.

Add :js:`"tx_cart_cart[shipping_same_as_billing]": $("#shipping-same-as-billing").is(":checked"),` to
post parameters of the `updateCountry` function.

.. index:: Fluid, Frontend, JavaScript
