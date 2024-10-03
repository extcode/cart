.. include:: ../../Includes.rst.txt

=====================================================
Feature: #223 - Add addToCart form framework finisher
=====================================================

See `Issue 223 <https://github.com/extcode/cart/issues/223>`__

Description
===========

In order to allow to individualize products when adding them to the cart, a new addToCart finisher for the form framework
allow to load a form and submit the form with the selected product. The fields are handled as frontend variants in the
cart product. They have no intended impact on the price or stock handling.

This feature supports all product extension but it's implemented for cart_events first. It allows to use different forms
to get some more information from the participants like first and last name, twitter handle or preferred food.

.. index:: API, Frontend, Backend, JavaScript
