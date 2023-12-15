.. include:: ../../../Includes.txt

=======
Caching
=======

A separate cache tag is set for all actions of the ProductController. This can
be used to specifically clear the cache of all pages with a product plugin.

PageTS:

.. code-block:: typoscript

   # clearCacheCmd for product folder with page id 35
   [globalVar = TSFE:id=35]
       TCEMAIN.clearCacheCmd = cacheTag:tx_cart
   [end]
