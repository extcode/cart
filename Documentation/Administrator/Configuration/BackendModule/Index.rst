.. include:: ../../../Includes.rst.txt

.. _backend_module:

==============
Backend module
==============

The backend module shows a list with filters. The date format of the input
fields of the filter can be adapted by overwriting the following TypoScript.

.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/constants.typoscript

   plugin.tx_cart {
       settings {
           backend {
              dateFormat = Y-m-d
           }
       }
   }

plugin.tx_cart.settings.search
--------------------------------

.. confval:: dateFormat

   :Type: string
   :Default: Y-m-d

   Defines the format which will be shown after using the filters in the
   backend.

   See also the `PHP documentation <https://www.php.net/manual/en/datetime.format.php>`_.
