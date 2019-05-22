.. include:: ../../Includes.txt

Installation
============

Installation using composer
---------------------------

The recommended way to install the extension is by using `Composer <https://getcomposer.org/>`_.
In your composer based TYPO3 project root, just do

`composer require extcode/cart`.

Installation from TYPO3 Extension Repository (TER)
--------------------------------------------------

Download and install the extension with the extension manager module.

Latest version from git
-----------------------
You can get the latest version from git by using the git command:

.. code-block:: bash

   git clone git@github.com:extcode/cart.git

Preparation: Include static TypoScript
--------------------------------------

The extension ships some TypoScript code which needs to be included.

#. Switch to the root page of your site.

#. Switch to the **Template module** and select *Info/Modify*.

#. Press the link **Edit the whole template record** and switch to the tab *Includes*.

#. Select **Shopping Cart - Cart** at the field *Include static (from extensions):*

Product Database / Product Storages
-----------------------------------

Cart itself doesn't provide any product database or product storage. You can use your own
product table or one of the product extension that I implemented for some use cases.

============================== ===================================================================================== =================================================================
extension key                  composer package                                                                      github repository
============================== ===================================================================================== =================================================================
cart_books                     `extcode/cart-books <https://packagist.org/packages/extcode/cart-books>`_             `extcode/cart_books <https://github.com/extcode/cart_books>`_
cart_events                    `extcode/cart-events <https://packagist.org/packages/extcode/cart-events>`_           `<https://github.com/extcode/cart_events>`_
cart_events_plus               ---                                                                                   ---
cart_gift_cards                ---                                                                                   ---
cart_product                   `extcode/cart-products <https://packagist.org/packages/extcode/cart-products>`_       `<https://github.com/extcode/cart_products>`_
============================== ===================================================================================== =================================================================

For own product storages you have to implement the \Extcode\Cart\Domain\Finisher\Cart\AddToCartFinisherInterface.
Please have a look at `Hooks <../../DeveloperManual/Hooks/Index.html>`__
