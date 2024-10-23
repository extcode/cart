.. include:: ../../Includes.rst.txt

=============================================================
Feature: #588 - Add event to modify buttons in backend module
=============================================================

See `Issue 588 <https://github.com/extcode/cart/issues/588>`__

Description
===========

Adding your own buttons to the backend module is not so easy in controllers. In
order to extend or replace the CSV export with an XML export, you need a way to
insert your own buttons if you do not want to develop your own backend module.

The OrderController for the administration in the backend has been extended by
the `\Extcode\Cart\Event\Template\Components\ModifyButtonBarEvent`. The buttons
in the OrderController have been moved to the
`\Extcode\Cart\EventListener\Template\Components\ModifyButtonBar` EventListener,
 which uses this event to decide which buttons should be displayed in which
 action based on the request.

Custom buttons can use the methods `getRequest()`, `getSettings()`,
`getSearchArguments()` and in the case of the `showAction` also `getOrderItem()`
to retrieve all the necessary information to insert custom buttons for custom
tasks.

Impact
======

No impact is expected as only private methods have changed.


.. index:: Backend
