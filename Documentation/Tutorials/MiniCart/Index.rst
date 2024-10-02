.. include:: ../../Includes.rst.txt

.. _minicart:

====================
Integrate a MiniCart
====================

:aspect:`Context:`
MiniCart is the (usually tiny) icon somewhere in the header of all pages which
shows how many items the customer has in the cart and which links the cart
itself.

.. NOTE::
   The following example will result in a page that is not fully cached.
   To get a fully cached site you would need to get the data via AJAX request.
   Have a look at :composer:`studiomitte/cart-count`.

.. code-block:: typoscript
   :caption: EXT:cartintegration/Configuration/TypoScript/setup.typoscript

   lib.miniCart = COA_INT
   lib.miniCart {
     10 = USER_INT
     10 {
       userFunc = TYPO3\CMS\Extbase\Core\Bootstrap->run
       extensionName = Cart
       pluginName = MiniCart
       vendorName = Extcode
     }
   }

.. code-block:: html
   :caption: EXT:sitepackage/Resources/Partials/Header.html

   <header>
      ...
      <!-- Somewhere in the header of your page -->
      <f:cObject typoscriptObjectPath="lib.miniCart"/>

      ...
   <header>
