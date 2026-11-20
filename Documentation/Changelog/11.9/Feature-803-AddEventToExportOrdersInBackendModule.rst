.. include:: ../../Includes.rst.txt

============================================================
Feature: #803 - Add event to export orders in backend module
============================================================

See `Issue 803 <https://github.com/extcode/cart/issues/803>`__

Description
===========

The `exportAction` of the backend `OrderController` dispatches the new
`\Extcode\Cart\Event\Order\ExportEvent`. An EventListener checks whether it is
responsible for the requested format and sets the response with the exported
orders.

The CSV export with the Fluid template `Backend/Order/Order/Export.csv` is
moved to the EventListener
`\Extcode\Cart\EventListener\Order\Export\FluidTemplate`. It renders the
template `Backend/Order/Order/Export.<format>` for every format for which the
template exists in the template root paths of the backend module.

Custom EventListeners can replace the export, e.g. to fetch the orders
directly from the database for a large number of orders, or add an export in
another format like XML. The button for another format can be added with the
`\Extcode\Cart\Event\Template\Components\ModifyButtonBarEvent`.

See :ref:`replace-order-export`.

Impact
======

No impact is expected. The Fluid template is still used by default, custom
templates in the template root paths of the backend module are used as before.

.. index:: Backend
