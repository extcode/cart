.. include:: ../Includes.rst.txt
.. _faq:

===
FAQ
===

.. rst-class:: panel panel-default

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

.. rst-class:: panel panel-default

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

.. rst-class:: panel panel-default

How can overwrite a translation?
================================

You can write your own XLIFF file where you are using the key of the translation
that you want to overwrite. Your XLIFF file needs to be registered in a
:php:`ext_localconf.php`, e.g. in your sitepackage.

The following example is for overwriting German translations:

.. code-block:: php
   :caption: EXT:sitepackage/ext_localconf.php

   $GLOBALS['TYPO3_CONF_VARS']['SYS']['locallangXMLOverride']
      ['de']['EXT:cart/Resources/Private/Language/locallang.xlf'][] =
         'EXT:sitepackage/Resources/Private/Language/Cart/de.locallang.xlf';

