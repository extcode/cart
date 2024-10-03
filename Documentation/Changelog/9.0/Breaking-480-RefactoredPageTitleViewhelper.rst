.. include:: ../../Includes.rst.txt

===============================================
Breaking: #480 - Refactored PageTitleViewhelper
===============================================

See `Issue 480 <https://github.com/extcode/cart/issues/480>`__

Description
===========

The existing `TitleTagViewHelper` was removed because it stopped working after
TYPO3 v10. The new ViewHelper adds the same functionality but its usage is
different.

Affected Installations
======================

The ViewHelper was not used in EXT:cart itself. But other extensions as e.g.
EXT:cart_products used it. Overwrites and own implementations of templates which
used `<cart:titleTag>` need adaption.

Migration
=========

Migrate the old usage of the ViewHelper as shown below

.. code-block:: html
   :caption: e.g. overwrite of EXT:cart_products/Resources/Private/Templates/Product/Show.html

   <!-- OLD IMPLEMENTATION -->
   <cart:format.nothing>
       <cart:titleTag>
           <f:format.htmlentitiesDecode>{product.title}</f:format.htmlentitiesDecode>
       </cart:titleTag>
   </cart:format.nothing>

   <!-- NEW IMPLEMENTATION -->
   <cart:titleTag pageTitle="{product.title}" />

.. index:: Template, Frontend
