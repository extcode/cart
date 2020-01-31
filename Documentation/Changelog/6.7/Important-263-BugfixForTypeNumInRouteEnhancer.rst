.. include:: ../../Includes.txt

=====================================================
Important: #263 - Bugfix for TypeNum in RouteEnhancer
=====================================================

See :issue:`263`

Description
===========

Adding a product to cart works correctly using AJAX and with a PageType RouteEnhancer.

Affected Installations
======================

All using RouteEnhancer.

Migration
=========

You can add following configuration. Please note, that the TypeNum is currently fixed for
compatibility reasons. However, a path segment other than `updatecart.html` or `updatecurrency.html` can be used.

.. code-block:: yaml

   routeEnhancers:
     PageTypeSuffix:
       type: PageType
       map:
         updatecart.html: 2278001
         updatecurrency.html: 2278003

.. index:: Fluid, TypoScript
