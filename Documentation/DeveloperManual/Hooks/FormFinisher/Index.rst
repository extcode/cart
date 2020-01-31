.. include:: ../../../Includes.txt

Form - AddToCartFinisher
------------------------

Cart calls the [$productType]['Form']['AddToCartFinisher'] where $productType is an unique identifiert for the product
type in the product extension. This Hook is used to get products from different product extensions.
The class has to implement the \Extcode\Cart\Domain\Finisher\Form\AddToCartFinisherInterface and has to provide at least
a method to get a \Extcode\Cart\Domain\Model\Cart\Product which can added to the cart.
This method is called from the AddToCart finisher of the TYPO3 from framework so a form can used to add individualized or
personalized products to Cart. All type of forms allows very complex individualizations.

This feature supports all product extension but it's implemented for cart_events first. It allows to use different forms
to get some more information from the participants like first and last name, twitter handle or preferred food.

.. IMPORTANT::
   Please note, that this is the first implementation. The methods and parameters can change in major versions.

I prepared some product extensions. They are using the following product types:

==================== ====================
extension            product type
==================== ====================
cart_events          CartEvents
==================== ====================

.. NOTICE::
   Instructions on how to use this for your own plugin will follow soon.