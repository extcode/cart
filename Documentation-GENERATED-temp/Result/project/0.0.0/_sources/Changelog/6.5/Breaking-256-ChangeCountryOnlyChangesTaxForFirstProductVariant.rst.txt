.. include:: ../../Includes.txt

==========================================================================
Breaking: #256 - Change country only changes tax for first product variant
==========================================================================

See :issue:`256`

Description
===========

An error is fixed which occurs in connection with the output of gross prices in
the product table of the shopping cart when products have several variants and
the change of the invoicing country assigns new tax classes to the products.
The tax class change now displays the correct gross price for all variants.

Affected Installations
======================

The use of the old template and JavaScripts has the consequence that the product
list is not updated if the gross price changes due to the change of the invoicing
country. This only affects installations in which the price of the product is
maintained as the net price.

Instances which use net price products and custom partial templates for:

:file:`EXT:cart/Resources/Private/Templates/Cart/Country/Update.html`

or own JavaScript instead of

:file:`EXT:cart/Resources/Public/JavaScripts/cart.js`.


Migration
=========

Add :html:`<f:render partial="Cart/ProductForm" arguments="{cart:cart}"/>` to your
:file:`EXT:cart/Resources/Private/Templates/Cart/Country/Update.html` template file

Add :js:`$("#form-cart").html($(data).filter("#form-cart").html());` to success
callback of the ajax request in `updateCountry` function.

.. index:: Fluid, Frontend, JavaScript
