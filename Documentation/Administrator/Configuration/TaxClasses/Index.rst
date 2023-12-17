.. include:: ../../../Includes.txt

===========
Tax classes
===========

Tax classes can be adapted, added and removed via TypoScript.
Three classes are predefined:

.. code-block:: typoscript
   :caption: EXT:cart/Configuration/TypoScript/constants.typoscript

   plugin.tx_cart {
      taxClasses {
           1 {
               value = 19
               calc = 0.19
               name = normal
           }
           2 {
               value = 7
               calc = 0.07
               name = reduced
           }
           3 {
               value = 0
               calc = 0.00
               name = free
           }
      }
