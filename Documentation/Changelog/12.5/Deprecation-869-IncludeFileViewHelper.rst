.. include:: ../../Includes.rst.txt

=========================================
Deprecation: #869 - IncludeFileViewHelper
=========================================

See `Issue 869 <https://github.com/extcode/cart/issues/869>`__

Description
===========

The view helper ``\Extcode\Cart\ViewHelpers\IncludeFileViewHelper``
(``<cart:includeFile>``) is deprecated. The view helpers ``<f:asset.css>`` and
``<f:asset.script>`` of the TYPO3 core include CSS and JavaScript files too and
support attributes, priorities and inline assets.

The templates of cart do not use the view helper. Its argument ``compress``
has no effect since TYPO3 v14.

Impact
======

Rendering the view helper triggers a deprecation. The view helper will be
removed in cart 13.0. Until then it still includes the file.

Affected Installations
======================

All installations with own templates which use ``<cart:includeFile>``, e.g.
templates copied from older versions of cart or of the cart extensions.

Migration
=========

Replace the view helper with ``<f:asset.css>`` for CSS files and
``<f:asset.script>`` for JavaScript files. Both need a unique
``identifier``:

.. code-block:: html

   <!-- before -->
   <cart:includeFile path="EXT:my_ext/Resources/Public/Stylesheets/cart.css" />
   <cart:includeFile path="EXT:my_ext/Resources/Public/JavaScript/cart.js" />

   <!-- after -->
   <f:asset.css identifier="myExtCart" href="EXT:my_ext/Resources/Public/Stylesheets/cart.css" />
   <f:asset.script identifier="myExtCart" src="EXT:my_ext/Resources/Public/JavaScript/cart.js" />

.. index:: Fluid, Frontend
