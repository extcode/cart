.. include:: ../../Includes.rst.txt

===============================================
Feature: #615 - Allow FeVariant to have a price
===============================================

See `Issue 615 <https://github.com/extcode/cart/issues/615>`__

Description
===========

The default `FeVariant` implementation cannot have a price. In order to be able
to process own `FeVariant`s with a price, the method arguments should use a
`FeVariantInterface` instead.

The `FeVariantWithPriceInterface` extends the `FeVariantInterface` with a
`getPrice()` method.
The price calculation of a product then takes into account whether the
`FeVariant` implements the `FeVariantWithPriceInterface` to add this price as
well.

To create an own `FeVariant` for a `Product`, you have to replace the
`CreateCartFrontendVariants` EventListener.

.. code-block:: yaml
   :caption: EXT:my_extension/Configuration/Services.yaml

   Extcode\CartProducts\EventListener\Create\CreateCartFrontendVariants: null

   MyVendor\MyExtension\EventListener\Create\CreateCartFrontendVariants:
     tags:
       - name: event.listener
         identifier: 'cart-products--create--create-cart-frontend-variants'
         event: Extcode\CartProducts\Event\RetrieveProductsFromRequestEvent
         after: 'cart-products--create--load-product'


This is very flexible. You can implement `FeVariant`s as checkboxes and add some
surcharge for some options. Another idea is to calculate the price markup
depending on the number of characters entered.How you can use this is up to you.
However, you should test carefully whether the price calculation for the product
price then fits, especially if you use more than one front-end variant with a
price for a product.

Impact
======

No Impact.

.. index:: Frontend, API
