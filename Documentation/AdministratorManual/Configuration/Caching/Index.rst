.. include:: ../../../Includes.txt

Caching
=======

Für alle Actions des ProductControllers wird ein eigener Cache Tag gesetzt. Dieser kann dazu genutzt werden, gezielt den
Cache aller Seiten mit einem Produkt Plugin zu leeren.

PageTS:

::

   # clearCacheCmd for product folder with page id 35
   [globalVar = TSFE:id=35]
       TCEMAIN.clearCacheCmd = cacheTag:tx_cart
   [end]
