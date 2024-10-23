.. include:: ../../Includes.rst.txt

==============================================================
Feature: #593 - Add event to assign own data to moduleTemplate
==============================================================

See `Issue 593 <https://github.com/extcode/cart/issues/593>`__

Description
===========

Adding your own data to module templates is not so easy. In order to assign
own data to the moduleTemplate, you need a way to assign your own data if you
do not want to develop your own backend module.

The `\Extcode\Cart\Event\Template\Components\ModifyModuleTemplateEvent` event
is dispached in the OrderController for the backend module.

An own EventListener can use the methods `getRequest()`, `getSettings()`,
`getModuleTemplate()` to retrieve all the necessary information to assign custom
data.
The request object can be used to assign data for specific actions.

Impact
======

No impact is expected.


.. index:: Backend
