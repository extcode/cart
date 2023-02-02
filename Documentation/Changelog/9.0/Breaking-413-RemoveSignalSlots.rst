.. include:: ../../Includes.txt

====================================
Breaking: #413 - Remove Signal Slots
====================================

See :issue:`413`

Description
===========

Signal slots have been removed in TYPO3 v12. These have been replaced by EventListeners where possible.
The extension offered following signal slots:

* `updateCountry` in `\Extcode\Cart\Utility\CartUtility`
* `updateCurrency` in `\Extcode\Cart\Utility\CurrencyUtility`
* `addProductAdditionalData`, `addBeVariantAdditionalData`, and `changeOrderItemBeforeSaving` in `\Extcode\Cart\Utility\OrderUtility`

Affected Installations
======================

All installations that used the signal slots to programmatically adjust the behavior of the extension are affected.

Migration
=========

* `updateCountry` in `\Extcode\Cart\Utility\CartUtility` can replaced with `\Extcode\Cart\EventListener\Cart\UpdateCountry`.
* `updateCurrency` in `\Extcode\Cart\Utility\CurrencyUtility` can replaced with `\Extcode\Cart\EventListener\Cart\UpdateCurrency`.
* `addProductAdditionalData`, `addBeVariantAdditionalData`, and `changeOrderItemBeforeSaving` in `\Extcode\Cart\Utility\OrderUtility`
  was used only directly in `\Extcode\Cart\EventListener\Order\Create\Order`. This class now dispatch the
  new `\Extcode\Cart\Event\Order\PersistOrderEvent`. Overwriting `\Extcode\Cart\Utility\OrderUtility` or using the signal
  slots of this class should thus be superfluous and can be replaced by your own EventListener. For more information
  check out the event documentation in the developer section.

.. index:: API