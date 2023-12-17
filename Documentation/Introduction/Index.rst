.. include:: ../Includes.txt
.. _introduction:

============
Introduction
============

Cart is an extension providing a shopping solution for TYPO3.

Description
===========

The extension is a small but powerful extension which "solely" adds a shopping
cart to your TYPO3 installation and is well suited for content commerce.

The extension allows you to add products to a cart and handles the order
process completely.

There are other awesome extensions to handle various types of products.
Furthermore, you will find some payment provider extensions to add
payment methods to the checkout process.
Have a look at :ref:`other_cart_extensions`.

Features
========

* Makes intensive use of the TYPO3 Core API functionality (is "TYPO3-native").
* Easily expandable:

  * Multiple PSR-14 events and interfaces
  * API (finisher pipeline) to process the order with possibility to register
    own tasks
  * API to add payment providers
  * API to connect your own product extensions

* Highly configurable through TypoScript
* Proved Bootstrap templates
* Backend module to show and utilize orders

Show cases
==========

Examples of websites which use this extension as e-commerce solution.

.. figure:: ../Images/Examples/liebman-design-import.com.png
   :width: 640
   :alt: Cart of Liebmann Design Import
   :class: with-shadow

   `www.liebman-design-import.com <https://www.liebman-design-import.com/warenkorb/>`__

.. figure:: ../Images/Examples/weingut-isele.de.png
   :width: 640
   :alt: Cart of Weingut Isele
   :class: with-shadow

   `www.weingut-isele.de <https://www.weingut-isele.de>`__

**Table of contents**

.. toctree::
   :maxdepth: 5
   :titlesonly:

   Support/Index
   Sponsoring/Index
   NoteOfThanks/Index
