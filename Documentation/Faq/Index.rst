.. include:: ../Includes.txt
.. _faq:

===
FAQ
===

How can I unset the predefined payment / shipping methods?
==========================================================

The following example unsets the default countries and payment methods and
add options for 2 other countries instead.

.. code-block:: typoscript
   :caption: EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
       settings {
           countries >
           countries {
               preset = 1
               options {
                   4 {
                       code = fr
                       label = France
                   }
                   5 {
                       code = nl
                       label = Netherlands
                   }
               }
           }
       }

       # equivalent done for shippings
       payments {
           countries {
               de >
               at >
               ch >
               fr {
                   preset = 1
                   options {
                       ..
                   }
               }
               nl < fr
           }
       }
   }

How can I add a field "Addition" for the addresses?
===================================================

As shown in :ref:`validating-and-hiding-fields` the field exists but is not
shown because the validation is set to `NotEmpty`.

The following configuration shows the "addition" field for the shipping address:

.. code-block:: typoscript
   caption: EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart.settings.validation.shippingAddress.fields {
       addition >
   }

How can overwrite a translation?
================================

This can be done in TypoScript, the following code snippet shows an example.

.. code-block:: typoscript
   caption: EXT:sitepackage/Configuration/TypoScript/setup.typoscript

   plugin.tx_cart {
     _LOCAL_LANG {
       de {
         tx_cart.mail.thank_you_for_order = Ganz herzlichen Dank für deine Bestellung!
       }
     }
   }

