.. include:: ../../Includes.rst.txt

====================================================
Feature: #593 - Add event to assign own data to view
====================================================

See `Issue 593 <https://github.com/extcode/cart/issues/593>`__

Description
===========

Adding your own data to plugin templates is not so easy. In order to assign
own data to the view, you need a lib in TypoScript and a f:cObject in the
template with some JavaScript on top.

This `\Extcode\Cart\Event\View\ModifyViewEvent` event is dispached in all
actions for which an output is generated via a template. Actions that are
forwarded to another action do not require the event.

An own EventListener can use the methods `getRequest()`, `getSettings()`,
`getView()` to retrieve all the necessary information to assign custom data.
The request object can be used to assign data for specific actions. For example,
saved addresses of a returning logged-in user can be displayed for selection
instead of the form for the billing address.

Impact
======

No impact is expected.


.. index:: Backend
