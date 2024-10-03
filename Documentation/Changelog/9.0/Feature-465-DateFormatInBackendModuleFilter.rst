.. include:: ../../Includes.rst.txt

============================================
Feature: #465 - DateFormat in Backend Module
============================================

See `Issue 465 <https://github.com/extcode/cart/issues/465>`__

Description
===========

The backend module was refactored. The dateformat of the date input fields can
be set in TypoScript.

.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/constants.typoscript

   plugin.tx_cart {
       settings {
           backend {
              dateFormat = Y-m-d
           }
       }
   }

Impact
======

Before this the dates in the backend were always in the format `d.m.Y`, now it
 is `Y-m-d` by default.
 The change affect only the formatting in the backend, not the storage format.

 Migration
 =========
 To get the old formatting you need to overwrite the above shown TypoScript
 constants:

 .. code-block:: typoscript
    :caption: EXT:sitepackage/Configuration/TypoScript/constants.typoscript

    plugin.tx_cart {
        settings {
            backend {
               dateFormat = d.m.Y
            }
        }
    }

.. index:: Backend
