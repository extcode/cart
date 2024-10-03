.. include:: ../../Includes.rst.txt

===========================================================
Breaking: #380 - TypoScript for countries in uniform format
===========================================================

See `Issue 380` and :issue:`435 <https://github.com/extcode/cart/issues/380` and :issue:`435>`__

Description
===========

Up to now it was possible to set the country specific configuration of shippings
and payments directly under :typoscript:`plugin.tx_cart.shippings` and
:typoscript:`plugin.tx_cart.payments`. Until the implementation of :issue:`435`
that's how it was done in the extension itself although the documentation
already showed the new structure.

This breaking change does no longer allow the old structure. As a result the
structure to set options is uniform in different places which makes it easier
for Integrators to set up this extension.

The uniform structure are now given for options in:

* :typoscript:`plugin.tx_cart.shippings` - see below
* :typoscript:`plugin.tx_cart.payment` - see below
* :typoscript:`plugin.tx_cart.settings.countries` - see :ref:`Breaking 437 (Allowed Countries)<breaking-437-allowedCountries>`
* :typoscript:`plugin.tx_cart.settings.currencies` - see :ref:`Breaking 437 (Currency)<breaking-437-currency>`

Affected Installations
======================

All installations which use the old structure ()which is shown below in the
migration description) are affected. The TypoScript needs to be adapted as shown
below.

Migration
=========

The country configuration needs to be wrapper in a :typoscript:`countries`
level.

The following two snippets show the BEFORE and AFTER implementation.

.. code-block:: typoscript
   :caption: BEFORE (in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript)

   plugin.tx_cart {
      shippings {
         de {
            preset = 1
            options {
               1 {
                  title = Standard
                  extra = 0.00
                  taxClassId = 1
                  status = open
               }
            }
         }
      }
      payments {
         de {
            preset = 1
            options {
               1 {
                  title = Pay in advance
                  extra = 0.00
                  taxClassId = 1
                  status = open
               }
            }
         }
      }
   }

.. code-block:: typoscript
   :caption: AFTER  (in e.g. EXT:sitepackage/Configuration/TypoScript/setup.typoscript)

   plugin.tx_cart {
      shippings {
         countries {
            de {
                preset = 1
                options {
                    1 {
                        title = Standard
                        extra = 0.00
                        taxClassId = 1
                        status = open
                    }
                 }
              }
           }
        }
      }
      payments {
          countries {
             de {
                preset = 1
                options {
                   1 {
                      title = Pay in advance
                      extra = 0.00
                      taxClassId = 1
                      status = open
                   }
                }
             }
          }
      }
   }

.. index:: Backend, TypoScript
