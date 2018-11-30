.. ==================================================
.. FOR YOUR INFORMATION
.. --------------------------------------------------
.. -*- coding: utf-8 -*- with BOM.

Hooks
-----

Cart - AddToCartFinisher
========================

Cart calls the [$productType]['Cart']['AddToCartFinisher'] where $productType is an unique identifiert for the product
type in the product extension. This Hook is used to get products from different product extensions.
The class has to implement the \Extcode\Cart\Domain\Finisher\Cart\AddToCartFinisherInterface and has to provide at least
a method to get a \Extcode\Cart\Domain\Model\Cart\Product which can added to the cart. In case of stock handling some
more methods has to be implemented to check availability.

.. IMPORTANT::
   Please note, that this is the first implementation. The methods and parameters can change in major versions.

I prepared some product extensions. They are using the following product types:

==================== ====================
extension            product type
==================== ====================
cart_books           CartBooks
cart_events          CartEvents
cart_gift_cards      CartGiftCards
cart_products        CartProducts
==================== ====================
