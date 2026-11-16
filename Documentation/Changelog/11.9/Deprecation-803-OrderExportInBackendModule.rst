.. include:: ../../Includes.rst.txt

==================================================
Deprecation: #803 - Order export in backend module
==================================================

See `Issue 803 <https://github.com/extcode/cart/issues/803>`__

Description
===========

The export of orders in the backend order module will be removed from the
cart extension with the version for TYPO3 v15. It is planned to provide the
export in a separate extension.

The following parts are deprecated:

- the button "Export (CSV)" in the backend order module
- the `exportAction` of `\Extcode\Cart\Controller\Backend\Order\OrderController`
- the event `\Extcode\Cart\Event\Order\ExportEvent`
- the EventListener `\Extcode\Cart\EventListener\Order\Export\FluidTemplate`
- the template `Resources/Private/Templates/Backend/Order/Order/Export.csv`
- the ViewHelpers `\Extcode\Cart\ViewHelpers\CsvHeaderViewHelper` and
  `\Extcode\Cart\ViewHelpers\CsvValuesViewHelper`

Instead of a file export, the cart extension should allow to hand over the
orders to an external system, e.g. an ERP or accounting software.

Impact
======

There is no impact yet. The export works as before until it is removed.

Affected Installations
======================

All installations which use the CSV export of the backend order module, which
override the template `Export.csv` or which register own EventListeners for
the `\Extcode\Cart\Event\Order\ExportEvent`.

Migration
=========

Nothing to do yet. Install the separate extension as soon as it is available.
Own EventListeners for the `\Extcode\Cart\Event\Order\ExportEvent` will have
to be adapted to the separate extension.

.. index:: Backend
