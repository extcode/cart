.. include:: ../../Includes.rst.txt


.. _images-cart-product-list:

=======================================
Images of products in cart product list
=======================================

Usually the cart of online shops shows a small image in the product list.
EXT:cart does this not by default because the use cases for the extension can be
quite diverse and a standard might not fit everyone.

It's easy to implement this functionality with an EventListener and a customized
partial.

The following snippets show an example where the first image of a product is
used:

.. code-block:: php
   :caption: EXT:cartintegration/Classes/EventListener/ExtendCartProduct.php

   <?php

   declare(strict_types=1);

   namespace Vendor\Cartintegration\EventListener\Cart\Create;

   use Extcode\CartProducts\Event\RetrieveProductsFromRequestEvent;

   class ExtendCartProduct
   {
       public function __invoke(RetrieveProductsFromRequestEvent $event): void
       {
           $productProduct = $event->getProductProduct();
           $cartProduct = $event->getCartProduct();

           // Using null safe operator to avoid errors
           $imageUid = $productProduct?->getFirstImage()?->getOriginalResource()?->getOriginalFile()?->getUid() ?? null;

           if ($imageUid) {
               $cartProduct->setAdditional(
                   'previewImage',
                   $imageUid
               );
           }
       }
   }


.. code-block:: yaml
   :caption: EXT:cartintegration/Configuration/Services.yaml

   services:
     _defaults:
       autowire: true
       autoconfigure: true
       public: false

     Vendor\Cartintegration\:
       resource: '../Classes/*'
       exclude: '../Classes/Domain/Model/*'

     Vendor\Cartintegration\EventListener\Cart\ExtendCartProduct:
       tags:
         - name: event.listener

.. code-block:: yaml
   :caption: EXT:cartintegration/Resources/Private/Plugins/Cart/Partials/Cart/ProductForm/ProductList.html

   // This snippet is simplified and needs adaption for your styling!

   <f:section name="withoutVariant">

       ...

       <f:if condition="{product.additionalArray.previewImage}">
           <f:image src="{product.additionalArray.previewImage}"/>
       </f:if>

   ...

   <f:section name="withVariant">

       ...

       <f:for each="{product.beVariants}" as="variant">
           <f:if condition="{product.additionalArray.previewImage}">
               <f:image src="{product.additionalArray.previewImage}"/>
           </f:if>

   ...
