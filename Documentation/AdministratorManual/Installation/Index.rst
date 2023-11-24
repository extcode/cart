.. include:: ../../Includes.txt
.. _installation:

============
Installation
============

.. _installation_extension:

Install the extension
=====================

Installation using composer
---------------------------

The recommended way to install the extension is by using `Composer <https://getcomposer.org/>`__.

In your composer-based TYPO3 project root, just do

.. code-block:: bash

   composer require extcode/cart

Installation from TYPO3 Extension Repository (TER)
--------------------------------------------------

Download and install the extension with the extension manager module.

Latest version from git
-----------------------
You can get the latest version from git by using the git command

.. code-block:: bash

   git clone git@github.com:extcode/cart.git

.. _installation_typoscript:

Include TypoScript
==================

The extension ships some TypoScript code which needs to be included.
There are two valid ways to do this:

Include TypoScript via TYPO3 backend
------------------------------------

#. Switch to the root page of your site.

#. Switch to the **Template module** and select *Info/Modify*.

#. Press the link **Edit the whole template record** and switch to the tab *Includes*.

#. Select **Shopping Cart - Cart** at the field *Include static (from extensions):*

Include TypoScript via SitePackage
----------------------------------
This way is preferred because the configuration is under version control.

#. Add :typoscript:`@import 'EXT:cart/Configuration/TypoScript/setup.typoscript'`
   to your  `sitepackage/Configuration/TypoScript/setup.typoscript`

#. Add :typoscript:`@import 'EXT:cart/Configuration/TypoScript/constants.typoscript'`
   to your  `sitepackage/Configuration/TypoScript/constants.typoscript`

Product Database / Product Storages
===================================

Cart itself doesn't provide any product database or product storage. You can use your own
product table or one of the given product extensions which are adapted for special use cases.

+----------------+----------------------------------------------------------------------------------+---------------------------------------------------------------------------------+
| Extension Key  | Composer Package                                                                 | Github Repository                                                               |
+================+==================================================================================+=================================================================================+
| `cart_product` | `extcode/cart-products <https://packagist.org/packages/extcode/cart-products>`__ | `github.com/extcode/cart_products <https://github.com/extcode/cart_products>`__ |
+----------------+----------------------------------------------------------------------------------+---------------------------------------------------------------------------------+
| `cart_books`   | `extcode/cart-books <https://packagist.org/packages/extcode/cart-books>`__       | `github.com/extcode/cart_books <https://github.com/extcode/cart_books>`__       |
+----------------+----------------------------------------------------------------------------------+---------------------------------------------------------------------------------+
| `cart_events`  | `extcode/cart-events <https://packagist.org/packages/extcode/cart-events>`__     | `github.com/extcode/cart_event <https://github.com/extcode/cart_events>`__      |
+----------------+----------------------------------------------------------------------------------+---------------------------------------------------------------------------------+


For own product storages you have to implement the `\Extcode\Cart\Domain\Finisher\Cart\AddToCartFinisherInterface`.
Please have a look at `Hooks <../../DeveloperManual/Hooks/Index.html>`__.
