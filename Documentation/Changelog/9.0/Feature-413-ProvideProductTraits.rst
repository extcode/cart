.. include:: ../../Includes.txt

======================================
Feature: #413 - Provide Product Traits
======================================

See :issue:`413`

Description
===========

Products can have several features like categories and tags or files and images
and some more. To ease the domain models in the extcode/cart-books, extcode/cart-events,
and extcode/cart-products the extcode/cart extension provides some Traits.

* `\Extcode\Cart\Domain\Model\Product\CategoryTrait`
* `\Extcode\Cart\Domain\Model\Product\FileAndImageTrait`
* `\Extcode\Cart\Domain\Model\Product\MeasureTrait`
* `\Extcode\Cart\Domain\Model\Product\ServiceAttributeTrait`
* `\Extcode\Cart\Domain\Model\Product\StockTrait`
* `\Extcode\Cart\Domain\Model\Product\TagTrait`

Impact
======

Negative effects are not expected.
The traits can also be used in own product extensions. With the use it is to be made certain that
the `__construct` method is not called and in the class, which uses appropriate Traits then the
ObjectStorages must initialize themselves.

.. index:: API, Backend