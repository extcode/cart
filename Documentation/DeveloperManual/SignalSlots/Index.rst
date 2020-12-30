.. include:: ../../Includes.txt

Signal Slots
------------

Controller Classes
==================

=============================================== ===================================== ==================================
Signal Class Name                               Signal Name                           Description
=============================================== ===================================== ==================================
Extcode\Cart\Controller\Order\PaymentController sendMailsAfterUpdatePaymentAndBefore  ...
Extcode\Cart\Utility\CurrencyUtility            sendMailsAfterUpdatePaymentAndAfter   ...
=============================================== ===================================== ==================================

Utility Classes
===============

======================================== ===================================== ==================================
Signal Class Name                        Signal Name                           Description
======================================== ===================================== ==================================
Extcode\Cart\Utility\CartUtility         updateCountry                         ...
Extcode\Cart\Utility\CurrencyUtility     updateCurrency                        ...
Extcode\Cart\Utility\OrderUtility        changeOrderItemBeforeSaving           ...
Extcode\Cart\Utility\OrderUtility        addBeVariantAdditionalData            ...
Extcode\Cart\Utility\ParserUtility       loadCartProductFromForeignDataStorage ...
======================================== ===================================== ==================================

.. IMPORTANT::
   Please note, that some methods and their Signal Slot will be moved to Finisher or removed completely in Version 7.x or 8.x.
